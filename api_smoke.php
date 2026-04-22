<?php

/**
 * Simple API smoke runner for migrated endpoints.
 *
 * Usage:
 *   php api_smoke.php --base="http://localhost/EventManagementSystem/public/api.php" --email="client@example.com" --password="Password1" [--booking=12]
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

function request(string $method, string $url, ?string $token = null, ?array $body = null): array
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
        CURLOPT_TIMEOUT => 25,
    ]);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $raw = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($errno !== 0) {
        return ['ok' => false, 'status' => 0, 'json' => null, 'error' => $error];
    }

    $json = json_decode((string)$raw, true);
    return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'json' => $json, 'error' => null];
}

function printResult(string $label, array $result): void
{
    $status = $result['status'] ?? 0;
    $ok = ($result['ok'] ?? false) ? 'PASS' : 'FAIL';
    echo sprintf("[%s] %s (HTTP %d)\n", $ok, $label, $status);

    if (!($result['ok'] ?? false)) {
        if (!empty($result['error'])) {
            echo "  transport_error: " . $result['error'] . "\n";
        } elseif (is_array($result['json'])) {
            $message = $result['json']['error']['message'] ?? json_encode($result['json']);
            echo "  error: " . $message . "\n";
        }
    }
}

$base = argValue($argv, '--base', 'http://localhost/EventManagementSystem/public/api.php');
$email = argValue($argv, '--email');
$password = argValue($argv, '--password');
$bookingId = argValue($argv, '--booking');

if (!$email || !$password) {
    fwrite(STDERR, "Missing required args --email and --password\n");
    exit(1);
}

echo "Base URL: {$base}\n";

$health = request('GET', $base . '/api/v1/health');
printResult('GET /api/v1/health', $health);

$login = request('POST', $base . '/api/v1/auth/login', null, [
    'email' => $email,
    'password' => $password,
]);
printResult('POST /api/v1/auth/login', $login);

$token = $login['json']['data']['token'] ?? null;
if (!$token) {
    echo "Cannot continue without JWT token.\n";
    exit(2);
}

$me = request('GET', $base . '/api/v1/auth/me', $token);
printResult('GET /api/v1/auth/me', $me);

$role = $me['json']['data']['user']['role'] ?? 'unknown';
echo "Authenticated role: {$role}\n";

$events = request('GET', $base . '/api/v1/events', $token);
printResult('GET /api/v1/events', $events);

$bookings = request('GET', $base . '/api/v1/bookings', $token);
printResult('GET /api/v1/bookings', $bookings);

$notiLatest = request('GET', $base . '/api/v1/notifications/latest', $token);
printResult('GET /api/v1/notifications/latest', $notiLatest);

$notiCounts = request('GET', $base . '/api/v1/notifications/counts', $token);
printResult('GET /api/v1/notifications/counts', $notiCounts);

if ($role === 'admin') {
    $dash = request('GET', $base . '/api/v1/dashboard/admin', $token);
    printResult('GET /api/v1/dashboard/admin', $dash);
} elseif ($role === 'organizer') {
    $dash = request('GET', $base . '/api/v1/dashboard/organizer', $token);
    printResult('GET /api/v1/dashboard/organizer', $dash);
} elseif ($role === 'client') {
    $dash = request('GET', $base . '/api/v1/dashboard/client', $token);
    printResult('GET /api/v1/dashboard/client', $dash);
}

if ($bookingId !== null && ctype_digit((string)$bookingId)) {
    $paySummary = request('GET', $base . '/api/v1/payments/' . $bookingId . '/summary', $token);
    printResult('GET /api/v1/payments/{bookingId}/summary', $paySummary);

    $payHistory = request('GET', $base . '/api/v1/payments/' . $bookingId . '/history', $token);
    printResult('GET /api/v1/payments/{bookingId}/history', $payHistory);
}

echo "Smoke run completed.\n";
