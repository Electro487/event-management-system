<?php

/**
 * ChatApiController
 * 
 * Handles OpenRouter AI chatbot requests with real-time database context.
 * POST /api/v1/chat
 */
class ChatApiController {
    private array $apiKeys = [];
    private array $models = [
        'anthropic/claude-3-haiku'
    ];

    public function __construct() {
        // Load API keys from .env (supports comma separated list)
        $envFile = dirname(__DIR__, 3) . '/.env';
        $env = file_exists($envFile) ? parse_ini_file($envFile) : [];
        $rawKeys = $env['OPENROUTER_API_KEY'] ?? '';

        if (!empty($rawKeys)) {
            // Split by comma and filter out empty strings
            $this->apiKeys = array_filter(array_map('trim', explode(',', $rawKeys)));
        }
    }

    /**
     * POST /api/v1/chat
     */
    public function chat(): void {
        $message = Request::input('message');
        
        if (empty($message)) {
            ApiResponse::error('Message is required', 400);
            return;
        }

        if (empty($this->apiKeys)) {
            error_log('[Chatbot] No OPENROUTER_API_KEY configured.');
            ApiResponse::error('Chatbot is not configured properly.', 500);
            return;
        }

        // Step A: Fetch real-time data from DB to "feed" the AI
        $context = $this->getSystemContext();

        // Step B: Call OpenRouter API with fallback logic (keys + models)
        $response = null;
        $lastError = 'Unknown error';

        foreach ($this->apiKeys as $key) {
            // Try each model with this key
            foreach ($this->models as $model) {
                $response = $this->callOpenRouter($message, $context, $key, $model);

                // If success, break both loops
                if (!isset($response['error'])) {
                    break 2;
                }

                // If error, check if it's worth retrying
                $lastError = $response['error']['message'] ?? 'API Error';
                $errCode = $response['error']['code'] ?? 0;

                error_log("[Chatbot] Model $model failed with key ..." . substr($key, -4) . ": $lastError (code: $errCode)");

                // Fallback to next model for most errors
                if (in_array($errCode, [401, 402, 429, 500, 503]) ||
                    stripos($lastError, 'credit') !== false ||
                    stripos($lastError, 'limit') !== false ||
                    stripos($lastError, 'insufficient') !== false ||
                    stripos($lastError, 'overloaded') !== false ||
                    stripos($lastError, 'unavailable') !== false ||
                    stripos($lastError, 'not found') !== false ||
                    $errCode >= 500) {
                    continue; // Try next model
                } else {
                    // For client errors (4xx except 401/402/429), stop trying this key
                    if ($errCode >= 400 && $errCode < 500 && !in_array($errCode, [401, 402, 429])) {
                        break;
                    }
                    continue; // Otherwise try next model
                }
            }
        }

        if (isset($response['error'])) {
            error_log('[Chatbot Final Error] ' . $lastError);
            ApiResponse::error('Assistant is currently overwhelmed. Please try again in a moment.', 500);
            return;
        }

        $reply = $response['choices'][0]['message']['content'] ?? 'I am sorry, I could not generate a response at this time.';
        ApiResponse::success(['reply' => $reply]);
    }

    /**
     * Generates a system context with live event data and deep platform business logic.
     */
    private function getSystemContext(): string {
        $eventModel = new Event();
        // Fetch up to 10 active events to provide context
        $activeEvents = $eventModel->getAllActiveEvents(null, null, 10);
        
        $context = "You are the e.PLAN Assistant, an elite, professional, and helpful AI concierge for the e.PLAN event management platform.\n";
        $context .= "e.PLAN is a premium event planning service specializing in Weddings, Corporate Meetings, Cultural Events, and Family Functions with architectural precision.\n\n";
        
        $context .= "### CORE RULES:\n";
        $context .= "1. PROFESSIONAL FOCUS: Only answer questions related to e.PLAN, events, booking, planning, or the platform's features. Politely decline unrelated queries.\n";
        $context .= "2. BRAND VOICE: Use a sophisticated, premium, yet helpful tone. Refer to the platform as 'e.PLAN' (case-sensitive).\n";
        $context .= "3. NO GUESSING: If you don't have specific info about a user's account, ask them to check their dashboard.\n\n";

        $context .= "### PAYMENT & REFUND POLICIES (CRITICAL):\n";
        $context .= "- EVENTS: Booking requires a **50% non-refundable advance** payment online via Stripe.\n";
        $context .= "- REMAINING BALANCE: The remaining **50% balance must be settled in cash** directly with the organizer on or before the event day.\n";
        $context .= "- CONCERTS/TICKETS: Concerts require **100% full payment** upfront online. Tickets are strictly non-refundable.\n";
        $context .= "- REFUNDS: All advanced payments and ticket purchases are **strictly non-refundable** upon cancellation.\n";
        $context .= "- SECURITY: All online transactions are securely processed via **Stripe**.\n\n";

        $context .= "### PLATFORM NAVIGATION & FEATURES:\n";
        $context .= "- BROWSE EVENTS: Found on the 'Browse Events' page. Users can explore all upcoming public events.\n";
        $context .= "- BOOKING FLOW: Select an event -> click 'View Details' -> choose a Tier (Silver, Gold, Platinum) -> 'Book Now' -> pay 50% advance via Stripe.\n";
        $context .= "- CUSTOM PACKAGES: If a standard tier doesn't fit, users can click 'Request Custom Package' on the event detail page to propose their own price and requirements.\n";
        $context .= "- MY BOOKINGS & TICKETS: Users can view their reservations, payment status, and download tickets (for concerts) from their Client Dashboard.\n";
        $context .= "- PROMO CODES: Activate by entering the code at the Checkout stage. They cannot be applied after payment.\n\n";
        
        if (!empty($activeEvents)) {
            $context .= "### CURRENT LIVE EVENTS:\n";
            foreach ($activeEvents as $event) {
                $date = date('M j, Y', strtotime($event['event_date']));
                $context .= "- {$event['title']} ({$event['category']}) at {$event['venue_name']} on {$date}.\n";
            }
            $context .= "\nUsers can view these and more on the Browse Events page.\n";
        }
        
        return $context;
    }

    /**
     * Makes the actual CURL request to OpenRouter.
     */
    private function callOpenRouter(string $message, string $context, string $apiKey, string $model = null): array {
        $url = 'https://openrouter.ai/api/v1/chat/completions';

        $data = [
            'model' => $model ?? $this->models[0],
            'messages' => [
                ['role' => 'system', 'content' => $context],
                ['role' => 'user', 'content' => $message]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
                'HTTP-Referer: http://localhost/EventManagementSystem', // Required by some OpenRouter models
                'X-Title: e.PLAN Assistant'
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['error' => ['message' => 'CURL Error: ' . $curlErr]];
        }

        $result = json_decode($response, true);
        if (!$result) {
            return ['error' => ['message' => 'Invalid API Response from OpenRouter']];
        }

        // Standardize error reporting for the fallback loop
        if ($httpCode >= 400 && !isset($result['error'])) {
            $result['error'] = ['message' => 'HTTP Error ' . $httpCode, 'code' => $httpCode];
        } elseif (isset($result['error'])) {
            // Ensure code is present for logic
            $result['error']['code'] = $result['error']['code'] ?? $httpCode;
        }

        return $result;
    }
}
