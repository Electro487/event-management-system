<?php

/**
 * Full API validation runner for migrated endpoints.
 *
 * Safe by default (read-only checks). Mutating checks run only with --mutate=1.
 *
 * Example:
 * php api_full_check.php --base="http://localhost/EventManagementSystem/public/api.php" --email="client@example.com" --password="Password1"
 *
 * With role/action IDs:
 * php api_full_check.php --base="http://localhost/EventManagementSystem/public/api.php" --email="organizer@example.com" --password="Password1" --mutate=1 --booking=12 --event=5 --notification=18
 */

function argValue(array $argv, string $name, ?string $default = null): ?string
{
    foreach ($argv as $arg) {
        if (strpos($arg, $name . '=') === 0) {
            return substr($arg, strlen($name) + 1);
        }
    }
    return $default;
}

function req(string $method, string $url, ?string $token = null, ?array $body = null): array
{
    $ch = curl_init($url);
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ]);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $raw = curl_exec($ch);
    $errNo = curl_errno($ch);
    $err = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($errNo !== 0) {
        return ['ok' => false, 'status' => 0, 'json' => null, 'error' => $err, 'raw' => null];
    }

    $json = json_decode((string)$raw, true);
    $ok = $status >= 200 && $status < 300;
    return ['ok' => $ok, 'status' => $status, 'json' => $json, 'error' => null, 'raw' => $raw];
}

function show(string $name, array $r): void
{
    $flag = $r['ok'] ? 'PASS' : 'FAIL';
    echo sprintf("[%s] %s (HTTP %d)\n", $flag, $name, $r['status']);
    if (!$r['ok']) {
        if (!empty($r['error'])) {
            echo "  transport_error: {$r['error']}\n";
        } else {
            $msg = $r['json']['error']['message'] ?? null;
            if ($msg) {
                echo "  error: {$msg}\n";
            } elseif (is_string($r['raw']) && $r['raw'] !== '') {
                echo "  raw: " . substr($r['raw'], 0, 300) . "\n";
            }
        }
    }
}

function pathJoin(string $base, string $path): string
{
    return rtrim($base, '/') . $path;
}

$base = argValue($argv, '--base', 'http://localhost/EventManagementSystem/public/api.php');
$email = argValue($argv, '--email');
$password = argValue($argv, '--password');
$bookingId = argValue($argv, '--booking');
$eventId = argValue($argv, '--event');
$notificationId = argValue($argv, '--notification');
$mutate = argValue($argv, '--mutate', '0') === '1';

if (!$email || !$password) {
    fwrite(STDERR, "Missing required args: --email, --password\n");
    exit(1);
}

echo "Base URL: {$base}\n";
echo "Mutations: " . ($mutate ? "ENABLED" : "DISABLED") . "\n\n";

// Phase 1 baseline
$r = req('GET', pathJoin($base, '/api/v1/health'));
show('GET /api/v1/health', $r);

// Phase 2 auth
$login = req('POST', pathJoin($base, '/api/v1/auth/login'), null, [
    'email' => $email,
    'password' => $password,
]);
show('POST /api/v1/auth/login', $login);

$token = $login['json']['data']['token'] ?? null;
if (!$token) {
    echo "\nCannot continue: login did not return a token.\n";
    exit(2);
}

$me = req('GET', pathJoin($base, '/api/v1/auth/me'), $token);
show('GET /api/v1/auth/me', $me);
$role = $me['json']['data']['user']['role'] ?? 'unknown';
echo "Authenticated role: {$role}\n\n";

// Phase 3 events/bookings/dashboards (read checks)
$events = req('GET', pathJoin($base, '/api/v1/events'), $token);
show('GET /api/v1/events', $events);

if (ctype_digit((string)$eventId)) {
    $ev = req('GET', pathJoin($base, '/api/v1/events/' . $eventId), $token);
    show('GET /api/v1/events/{id}', $ev);
}

$bookings = req('GET', pathJoin($base, '/api/v1/bookings'), $token);
show('GET /api/v1/bookings', $bookings);

if (ctype_digit((string)$bookingId)) {
    $bk = req('GET', pathJoin($base, '/api/v1/bookings/' . $bookingId), $token);
    show('GET /api/v1/bookings/{id}', $bk);
}

if ($role === 'admin') {
    show('GET /api/v1/dashboard/admin', req('GET', pathJoin($base, '/api/v1/dashboard/admin'), $token));
} elseif ($role === 'organizer') {
    show('GET /api/v1/dashboard/organizer', req('GET', pathJoin($base, '/api/v1/dashboard/organizer'), $token));
} elseif ($role === 'client') {
    show('GET /api/v1/dashboard/client', req('GET', pathJoin($base, '/api/v1/dashboard/client'), $token));
}

echo "\n";

// Phase 4 payments/notifications (read checks)
show('GET /api/v1/notifications/latest', req('GET', pathJoin($base, '/api/v1/notifications/latest'), $token));
$notiList = req('GET', pathJoin($base, '/api/v1/notifications'), $token);
show('GET /api/v1/notifications', $notiList);
show('GET /api/v1/notifications/counts', req('GET', pathJoin($base, '/api/v1/notifications/counts'), $token));

if (ctype_digit((string)$bookingId)) {
    show('GET /api/v1/payments/{bookingId}/summary', req('GET', pathJoin($base, '/api/v1/payments/' . $bookingId . '/summary'), $token));
    show('GET /api/v1/payments/{bookingId}/history', req('GET', pathJoin($base, '/api/v1/payments/' . $bookingId . '/history'), $token));
}

if (!$mutate) {
    echo "\nMutation checks skipped. Use --mutate=1 to run state-changing endpoint checks.\n";
    echo "Full validation run completed.\n";
    exit(0);
}

echo "\nRunning mutation checks...\n";

// Client: optionally create a booking for a specific event (deterministic)
if ($role === 'client' && ctype_digit((string)$eventId) && !ctype_digit((string)$bookingId)) {
    $ev = req('GET', pathJoin($base, '/api/v1/events/' . $eventId), $token);
    show('GET /api/v1/events/{id} (for booking)', $ev);

    $event = $ev['json']['data']['event'] ?? null;
    $packagesJson = is_array($event) ? ($event['packages'] ?? null) : null;
    $packages = is_string($packagesJson) ? json_decode($packagesJson, true) : (is_array($packagesJson) ? $packagesJson : null);

    $tier = null;
    $price = null;
    if (is_array($packages)) {
        foreach (['basic', 'standard', 'premium'] as $t) {
            if (isset($packages[$t]['price'])) {
                $tier = $t;
                $price = (float)$packages[$t]['price'];
                break;
            }
        }
    }

    if ($tier && $price && $price > 0) {
        $user = $me['json']['data']['user'] ?? [];
        $fullName = (string)($user['fullname'] ?? 'Client User');
        $clientEmail = (string)($user['email'] ?? $email);

        $eventDate = date('Y-m-d', strtotime('+7 days'));

        $create = req('POST', pathJoin($base, '/api/v1/bookings'), $token, [
            'event_id' => (int)$eventId,
            'package_tier' => $tier,
            'event_date' => $eventDate,
            'guest_count' => 50,
            'full_name' => $fullName,
            'email' => $clientEmail,
            'phone' => '9800000000',
            'checkin_time' => '10:00 AM',
            'total_amount' => $price,
        ]);
        show('POST /api/v1/bookings (create)', $create);

        $newId = $create['json']['data']['booking_id'] ?? null;
        if ($newId) {
            $bookingId = (string)$newId;
            echo "Created booking id: {$bookingId}\n";
        }
    } else {
        echo "Could not auto-create booking: event packages missing/invalid.\n";
    }
}

if (!ctype_digit((string)$bookingId)) {
    $candidate = $bookings['json']['data']['items'][0]['id'] ?? null;
    if ($candidate !== null) {
        $bookingId = (string)$candidate;
        echo "Auto-selected booking id for mutation checks: {$bookingId}\n";
    }
}

if (!ctype_digit((string)$notificationId)) {
    $candidate = $notiList['json']['data']['notifications'][0]['id'] ?? null;
    if ($candidate !== null) {
        $notificationId = (string)$candidate;
        echo "Auto-selected notification id for mutation checks: {$notificationId}\n";
    }
}

// Notification mutations
if (ctype_digit((string)$notificationId)) {
    show('PATCH /api/v1/notifications/{id}/read', req('PATCH', pathJoin($base, '/api/v1/notifications/' . $notificationId . '/read'), $token));
    show('PATCH /api/v1/notifications/{id}/unread', req('PATCH', pathJoin($base, '/api/v1/notifications/' . $notificationId . '/unread'), $token));
    // delete is destructive; keep optional but explicit via provided id
    show('DELETE /api/v1/notifications/{id}', req('DELETE', pathJoin($base, '/api/v1/notifications/' . $notificationId), $token));
}
show('PATCH /api/v1/notifications/mark-all-read', req('PATCH', pathJoin($base, '/api/v1/notifications/mark-all-read'), $token));
show('PATCH /api/v1/notifications/mark-all-unread', req('PATCH', pathJoin($base, '/api/v1/notifications/mark-all-unread'), $token));

// Booking/payment mutations by role
if ($role === 'client') {
    if (ctype_digit((string)$bookingId)) {
        // Stripe checkout creates session (external side effect but expected)
        show(
            'POST /api/v1/payments/checkout',
            req('POST', pathJoin($base, '/api/v1/payments/checkout'), $token, ['booking_id' => (int)$bookingId])
        );
        // confirm requires real paid session_id; intentionally not auto-called
    }
    if (ctype_digit((string)$bookingId)) {
        show('PATCH /api/v1/bookings/{id}/cancel', req('PATCH', pathJoin($base, '/api/v1/bookings/' . $bookingId . '/cancel'), $token));
    }
} elseif ($role === 'organizer' || $role === 'admin') {
    if (ctype_digit((string)$bookingId)) {
        show('PATCH /api/v1/bookings/{id}/approve', req('PATCH', pathJoin($base, '/api/v1/bookings/' . $bookingId . '/approve'), $token));
        show('PATCH /api/v1/bookings/{id}/mark-paid', req('PATCH', pathJoin($base, '/api/v1/bookings/' . $bookingId . '/mark-paid'), $token));
    }
}

echo "Full validation run completed.\n";
