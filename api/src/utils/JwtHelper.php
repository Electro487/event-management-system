<?php

class JwtHelper
{
    private static function config(): array
    {
        return require dirname(__DIR__) . '/config/jwt.php';
    }

    public static function issue(array $claims): string
    {
        $config = self::config();
        $now = time();

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = array_merge($claims, [
            'iss' => $config['issuer'],
            'iat' => $now,
            'exp' => $now + max(60, (int)$config['ttl_seconds']),
        ]);

        $segments = [
            self::base64UrlEncode(json_encode($header)),
            self::base64UrlEncode(json_encode($payload)),
        ];

        $signingInput = implode('.', $segments);
        $signature = hash_hmac('sha256', $signingInput, $config['secret'], true);
        $segments[] = self::base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public static function verify(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('Invalid token format.');
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;
        $config = self::config();

        $expected = self::base64UrlEncode(hash_hmac(
            'sha256',
            $headerB64 . '.' . $payloadB64,
            $config['secret'],
            true
        ));

        if (!hash_equals($expected, $signatureB64)) {
            throw new RuntimeException('Invalid token signature.');
        }

        $payloadJson = self::base64UrlDecode($payloadB64);
        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            throw new RuntimeException('Invalid token payload.');
        }

        if (($payload['iss'] ?? null) !== $config['issuer']) {
            throw new RuntimeException('Invalid token issuer.');
        }

        if (($payload['exp'] ?? 0) < time()) {
            throw new RuntimeException('Token expired.');
        }

        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }
}
