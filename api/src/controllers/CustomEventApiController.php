<?php
class CustomEventApiController
{
    private $requestModel;
    private $messageModel;
    private $notificationModel;
    private $userModel;
    private $eventModel;

    public function __construct()
    {
        $this->requestModel = new CustomEventRequest();
        $this->messageModel = new Message();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
        $this->eventModel = new Event();
    }

    public function store(): void
    {
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }

        $groupEventId = Request::input('group_event_id');
        $organizerId = Request::input('organizer_id');
        $baseTier = Request::input('base_package_tier');
        $eventDate = Request::input('event_date');
        $guestCount = Request::input('guest_count');
        $customPkgs = Request::input('custom_packages', '{}');
        $proposedPrice = Request::input('proposed_price');
        $initialMsg = Request::input('initial_message');

        if (empty($groupEventId) || empty($baseTier) || empty($proposedPrice) || empty($organizerId)) {
            ApiResponse::error('Missing required fields', 400);
            return;
        }

        $reqData = [
            'group_event_id' => $groupEventId,
            'client_id' => $user['id'],
            'organizer_id' => $organizerId,
            'base_package_tier' => $baseTier,
            'event_date' => $eventDate,
            'guest_count' => $guestCount,
            'custom_packages' => $customPkgs,
            'proposed_price' => $proposedPrice,
            'status' => 'pending'
        ];

        $requestId = $this->requestModel->create($reqData);

        if ($requestId) {
            $event = $this->eventModel->getById($groupEventId);
            $eventTitle = $event['title'] ?? 'Custom Event';
            $clientName = $user['fullname'] ?? 'A client';

            // Notify Organizer
            $this->notificationModel->create($organizerId, 'New Custom Event Request', "{$clientName} requested a custom package for '{$eventTitle}'.", 'custom_request', $requestId);
            
            // Notify Admins
            foreach ($this->userModel->getAdmins() as $admin) {
                $this->notificationModel->create($admin['id'], 'New Custom Request Received', "{$clientName} requested a custom package for '{$eventTitle}'.", 'custom_request', $requestId);
            }

            if (!empty($initialMsg)) {
                $this->messageModel->create([
                    'sender_id' => $user['id'],
                    'receiver_id' => $organizerId,
                    'request_id' => $requestId,
                    'message' => $initialMsg
                ]);
            }
            ApiResponse::success(['id' => $requestId], 201);
            return;
        }

        ApiResponse::error('Failed to create request', 500);
    }

    public function index(): void
    {
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }

        if ($user['role'] === 'client') {
            $requests = $this->requestModel->getByClientId($user['id']);
        } else {
            $requests = $this->requestModel->getByOrganizerId($user['id']);
        }

        ApiResponse::success(['requests' => $requests]);
    }

    public function show(): void
    {
        $id = Request::param('id');
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }

        $request = $this->requestModel->getById($id);
        if (!$request) {
            ApiResponse::error('Request not found', 404);
            return;
        }

        if ($request['client_id'] != $user['id'] && $request['organizer_id'] != $user['id'] && $user['role'] !== 'admin') {
            ApiResponse::error('Unauthorized access to request', 403);
            return;
        }

        $messages = $this->messageModel->getByRequestId($id);

        ApiResponse::success([
            'request' => $request,
            'messages' => $messages
        ]);
    }

    public function sendMessage(): void
    {
        $id = Request::param('id');
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }

        $request = $this->requestModel->getById($id);
        if (!$request) {
            ApiResponse::error('Request not found', 404);
            return;
        }

        $message = Request::input('message');
        if (empty($message)) {
            ApiResponse::error('Message content required', 400);
            return;
        }

        $receiverId = ($user['id'] == $request['client_id']) ? $request['organizer_id'] : $request['client_id'];

        $msgId = $this->messageModel->create([
            'sender_id' => $user['id'],
            'receiver_id' => $receiverId,
            'request_id' => $id,
            'message' => $message
        ]);

        if ($msgId) {
            $eventTitle = $request['event_title'] ?? 'Custom Event';
            $senderName = $user['fullname'] ?? 'Someone';
            $this->notificationModel->create($receiverId, 'New Message', "{$senderName} sent you a message regarding '{$eventTitle}'.", 'custom_message', $id);
            
            ApiResponse::success(['id' => $msgId], 200);
            return;
        }

        ApiResponse::error('Failed to send message', 500);
    }

    public function updateStatus(): void
    {
        $id = Request::param('id');
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }

        $status = Request::input('status');
        if (empty($status)) {
            ApiResponse::error('Status required', 400);
            return;
        }

        $request = $this->requestModel->getById($id);
        if (!$request) {
             ApiResponse::error('Request not found', 404);
             return;
        }

        if ($user['role'] === 'client' && !in_array($status, ['booked', 'cancelled'])) {
             ApiResponse::error('Clients can only book or cancel', 403);
             return;
        }

        if ($this->requestModel->updateStatus($id, $status)) {
             $eventTitle = $request['event_title'] ?? 'Custom Event';
             $actorName = $user['fullname'] ?? 'The system';
             
             if ($user['id'] == $request['client_id']) {
                 // Client updated (e.g. booked/cancelled)
                 $this->notificationModel->create($request['organizer_id'], 'Request Status Updated', "Client {$actorName} updated the status of '{$eventTitle}' to " . strtoupper($status), 'custom_status', $id);
             } else {
                 // Organizer or Admin updated
                 $this->notificationModel->create($request['client_id'], 'Request Status Updated', "{$actorName} updated the status of your '{$eventTitle}' request to " . strtoupper($status), 'custom_status', $id);
             }

             // Always notify admins if not the actor
             foreach ($this->userModel->getAdmins() as $admin) {
                 if ($user['id'] != $admin['id']) {
                     $this->notificationModel->create($admin['id'], 'Custom Request Update', "{$actorName} updated '{$eventTitle}' status to " . strtoupper($status), 'custom_status', $id);
                 }
             }

             ApiResponse::success(['message' => 'Status updated']);
             return;
        }

        ApiResponse::error('Failed to update status', 500);
    }

    public function updateOffer(): void
    {
        $id = Request::param('id');
        $user = $GLOBALS['api_auth_user'] ?? null;
        if (!$user) {
            ApiResponse::error('Unauthorized', 401);
            return;
        }

        $request = $this->requestModel->getById($id);
        if (!$request) {
            ApiResponse::error('Request not found', 404);
            return;
        }

        if ($request['organizer_id'] != $user['id'] && $user['role'] !== 'admin') {
            ApiResponse::error('Only the organizer can update the offer', 403);
            return;
        }

        $proposedPrice = Request::input('proposed_price');
        $customPackages = Request::input('custom_packages');
        
        if (empty($proposedPrice) || empty($customPackages)) {
             ApiResponse::error('Proposed price and packages are required', 400);
             return;
        }

        if ($this->requestModel->updatePriceAndPackage($id, $proposedPrice, $customPackages)) {
            $this->requestModel->updateStatus($id, 'negotiating');
            
            $eventTitle = $request['event_title'] ?? 'Custom Event';
            $this->notificationModel->create($request['client_id'], 'New Offer Received', "The organizer has sent a new offer for '{$eventTitle}'. Check the negotiation thread.", 'custom_offer', $id);
            
            ApiResponse::success(['message' => 'Offer updated successfully']);
            return;
        }

        ApiResponse::error('Failed to update offer', 500);
    }
}

