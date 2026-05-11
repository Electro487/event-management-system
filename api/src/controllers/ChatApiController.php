<?php

/**
 * ChatApiController
 * 
 * Handles OpenRouter AI chatbot requests with real-time database context.
 * POST /api/v1/chat
 */
class ChatApiController {
    private string $apiKey;
    private string $model = 'inclusionai/ring-2.6-1t:free';

    public function __construct() {
        // Load API key from .env
        $envFile = dirname(__DIR__, 3) . '/.env';
        $env = file_exists($envFile) ? parse_ini_file($envFile) : [];
        $this->apiKey = $env['OPENROUTER_API_KEY'] ?? '';
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

        if (empty($this->apiKey)) {
            error_log('[Chatbot] OPENROUTER_API_KEY is not configured.');
            ApiResponse::error('Chatbot is not configured properly.', 500);
            return;
        }

        // Step A: Fetch real-time data from DB to "feed" the AI
        $context = $this->getSystemContext();

        // Step B: Call OpenRouter API
        $response = $this->callOpenRouter($message, $context);

        if (isset($response['error'])) {
            $errMsg = $response['error']['message'] ?? 'API Error';
            error_log('[Chatbot OpenRouter Error] ' . $errMsg);
            ApiResponse::error($errMsg, 500);
            return;
        }

        $reply = $response['choices'][0]['message']['content'] ?? 'I am sorry, I could not generate a response at this time.';
        ApiResponse::success(['reply' => $reply]);
    }

    /**
     * Generates a system context with live event data from the database.
     */
    private function getSystemContext(): string {
        $eventModel = new Event();
        // Fetch up to 10 active events to provide context
        $activeEvents = $eventModel->getAllActiveEvents(null, null, 10);
        
        $context = "You are the e.PLAN Assistant, a friendly and professional chatbot for the e.PLAN event management platform.\n";
        $context .= "e.PLAN specializes in architectural precision for every milestone, including Weddings, Meetings, Cultural Events, and Family Functions.\n";
        $context .= "Answer questions concisely and professionally. If you don't know something, be honest about it.\n\n";
        
        if (!empty($activeEvents)) {
            $context .= "Current Live Events in our database that you can mention:\n";
            foreach ($activeEvents as $event) {
                $date = date('M j, Y', strtotime($event['event_date']));
                $context .= "- {$event['title']} ({$event['category']}) at {$event['venue_name']}, {$event['venue_location']} on {$date}.\n";
            }
            $context .= "\nUsers can browse all events and book them through the 'Browse Events' page.\n";
        } else {
            $context .= "We currently have various events being planned. Users can check the 'Browse Events' page for the latest updates.\n";
        }
        
        return $context;
    }

    /**
     * Makes the actual CURL request to OpenRouter.
     */
    private function callOpenRouter(string $message, string $context): array {
        $url = 'https://openrouter.ai/api/v1/chat/completions';
        
        $data = [
            'model' => $this->model,
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
                'Authorization: Bearer ' . $this->apiKey,
                'HTTP-Referer: http://localhost/EventManagementSystem', // Required by some OpenRouter models
                'X-Title: e.PLAN Assistant'
            ],
            CURLOPT_TIMEOUT        => 45,
        ]);

        $response = curl_exec($ch);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['error' => ['message' => 'CURL Error: ' . $curlErr]];
        }

        return json_decode($response, true) ?: ['error' => ['message' => 'Invalid API Response from OpenRouter']];
    }
}
