<?php

class EventService
{
    private Event $eventModel;
    private Notification $notificationModel;
    private User $userModel;

    public function __construct()
    {
        $this->eventModel = new Event();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }

    public function list(array $authUser, ?string $category, ?string $search, int $page, int $limit): array
    {
        $offset = max(0, ($page - 1) * $limit);
        if ($authUser['role'] === 'client') {
            return [
                'ok' => true,
                'status' => 200,
                'data' => [
                    'items' => $this->eventModel->getAllActiveEvents($category, $search, $limit, $offset),
                    'total' => $this->eventModel->countActiveEvents($category, $search),
                    'page' => $page,
                    'limit' => $limit,
                ],
            ];
        }

        if ($authUser['role'] === 'organizer') {
            return ['ok' => true, 'status' => 200, 'data' => ['items' => $this->eventModel->getAllByOrganizer($authUser['id'])]];
        }

        return ['ok' => true, 'status' => 200, 'data' => ['items' => $this->eventModel->getAll()]];
    }

    public function detail(array $authUser, int $id): array
    {
        $event = $this->eventModel->getById($id);
        if (!$event) {
            return ['ok' => false, 'status' => 404, 'message' => 'Event not found.'];
        }
        if ($authUser['role'] === 'client' && ($event['status'] ?? '') !== 'active') {
            return ['ok' => false, 'status' => 404, 'message' => 'Event not found.'];
        }
        if ($authUser['role'] === 'organizer' && (int)$event['organizer_id'] !== (int)$authUser['id']) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }

        return ['ok' => true, 'status' => 200, 'data' => ['event' => $event]];
    }

    public function create(array $authUser, array $payload): array
    {
        if (!in_array($authUser['role'], ['admin', 'organizer'], true)) {
            return ['ok' => false, 'status' => 403, 'message' => 'Only admin/organizer can create events.'];
        }

        $eventData = $this->buildEventData($authUser, $payload);
        $eventId = $this->eventModel->create($eventData);
        if (!$eventId) {
            return ['ok' => false, 'status' => 500, 'message' => 'Database insertion failed.'];
        }

        $title = $eventData['title'];
        $allClients = $this->userModel->getClients();
        $admins = $this->userModel->getAdmins();

        if ($authUser['role'] === 'organizer') {
            foreach ($admins as $admin) {
                $this->notificationModel->create(
                    $admin['id'],
                    'New Event Campaign',
                    "Organizer {$authUser['fullname']} has created a new event: {$title}.",
                    'event',
                    $eventId
                );
            }
            foreach ($allClients as $client) {
                $this->notificationModel->create(
                    $client['id'],
                    'New Event Launched!',
                    "A new event '{$title}' has been created by {$authUser['fullname']}. Check it out!",
                    'event',
                    $eventId
                );
            }
        } else {
            foreach ($allClients as $client) {
                $this->notificationModel->create(
                    $client['id'],
                    'New Event Launched by Admin!',
                    "A new official event '{$title}' has been created. Register now!",
                    'event',
                    $eventId
                );
            }
        }

        return ['ok' => true, 'status' => 201, 'data' => ['event_id' => (int)$eventId]];
    }

    public function update(array $authUser, int $id, array $payload): array
    {
        $existing = $this->eventModel->getById($id);
        if (!$existing) {
            return ['ok' => false, 'status' => 404, 'message' => 'Event not found.'];
        }
        if ($authUser['role'] === 'organizer' && (int)$existing['organizer_id'] !== (int)$authUser['id']) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }
        if (!in_array($authUser['role'], ['admin', 'organizer'], true)) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }

        $eventData = $this->buildEventData($authUser, $payload, $existing);
        $ok = $this->eventModel->update($id, $eventData);
        if (!$ok) {
            return ['ok' => false, 'status' => 500, 'message' => 'Update failed.'];
        }

        $allClients = $this->userModel->getClients();
        foreach ($allClients as $client) {
            $this->notificationModel->create(
                $client['id'],
                $authUser['role'] === 'admin' ? 'Event Details Updated by Admin' : 'Event Details Updated',
                "The event '{$existing['title']}' details were updated. Please review.",
                'event_update',
                $id
            );
        }

        return ['ok' => true, 'status' => 200, 'data' => ['updated' => true]];
    }

    public function delete(array $authUser, int $id): array
    {
        $event = $this->eventModel->getById($id);
        if (!$event) {
            return ['ok' => false, 'status' => 404, 'message' => 'Event not found.'];
        }
        if ($authUser['role'] === 'organizer' && (int)$event['organizer_id'] !== (int)$authUser['id']) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }
        if (!in_array($authUser['role'], ['admin', 'organizer'], true)) {
            return ['ok' => false, 'status' => 403, 'message' => 'Forbidden.'];
        }

        $bookingModel = new Booking();
        $clientIds = $bookingModel->getClientsByEvent($id);
        if (!$this->eventModel->delete($id)) {
            return ['ok' => false, 'status' => 500, 'message' => 'Delete failed.'];
        }

        foreach ($clientIds as $clientId) {
            $this->notificationModel->create(
                (int)$clientId,
                $authUser['role'] === 'admin' ? 'Event Cancelled by Administration' : 'Event Cancelled by Organizer',
                "Sorry, the event '{$event['title']}' has been removed.",
                'event_delete',
                0
            );
        }

        return ['ok' => true, 'status' => 200, 'data' => ['deleted' => true]];
    }

    private function buildEventData(array $authUser, array $payload, array $existing = []): array
    {
        $packages = $this->validatePackagePricing($payload['packages'] ?? []);
        $organizerId = $payload['organizer_id'] ?? ($existing['organizer_id'] ?? $authUser['id']);

        return [
            'organizer_id' => $organizerId,
            'title' => trim((string)($payload['title'] ?? ($existing['title'] ?? ''))),
            'description' => trim((string)($payload['description'] ?? ($existing['description'] ?? ''))),
            'category' => trim((string)($payload['category'] ?? ($existing['category'] ?? ''))),
            'status' => trim((string)($payload['status'] ?? ($existing['status'] ?? 'draft'))),
            'event_date' => null,
            'venue_name' => trim((string)($payload['venue_name'] ?? ($existing['venue_name'] ?? ''))),
            'venue_location' => trim((string)($payload['venue_location'] ?? ($existing['venue_location'] ?? ''))),
            'image_path' => $this->handleImageUpload($payload['image'] ?? null) ?: ($payload['image_path'] ?? ($existing['image_path'] ?? null)),
            'packages' => json_encode($packages),
        ];
    }

    private function handleImageUpload(?array $fileInfo): ?string
    {
        if ($fileInfo && isset($fileInfo['error']) && $fileInfo['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 3) . '/public/assets/images/events/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExtension = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
            $fileName = 'event_' . uniqid() . '_' . time() . '.' . $fileExtension;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($fileInfo['tmp_name'], $targetFile)) {
                return '/EventManagementSystem/public/assets/images/events/' . $fileName;
            }
        }
        return null;
    }

    private function validatePackagePricing($packages): array
    {
        if (!is_array($packages)) {
            throw new InvalidArgumentException('Invalid package data submitted.');
        }

        $tiers = ['basic', 'standard', 'premium'];
        $prices = [];
        foreach ($tiers as $tier) {
            if (!isset($packages[$tier]['price']) || !preg_match('/^\d+$/', (string)$packages[$tier]['price'])) {
                throw new InvalidArgumentException(ucfirst($tier) . ' package price must be a whole number only.');
            }
            $price = (int)$packages[$tier]['price'];
            if ($price <= 0) {
                throw new InvalidArgumentException(ucfirst($tier) . ' package price must be greater than 0.');
            }
            $prices[$tier] = $price;
            $packages[$tier]['price'] = $price;
        }

        if (!($prices['basic'] < $prices['standard'] && $prices['standard'] < $prices['premium'])) {
            throw new InvalidArgumentException('Price order must be: Basic < Standard < Premium.');
        }

        return $packages;
    }
}
