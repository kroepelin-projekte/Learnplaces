<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator;

use Random\RandomException;

class TokenHandler
{

    /**
     * @throws RandomException
     */
    public function createSecret(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function encode(array $payload, string $secret): string
    {
        $headers_encoded = $this->base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]));
        $payload_encoded = $this->base64UrlEncode(json_encode($payload));
        $signature_encoded = $this->base64UrlEncode(
            hash_hmac(
                'sha256',
                "$headers_encoded.$payload_encoded",
                $secret,
                true
            )
        );
        return "$headers_encoded.$payload_encoded.$signature_encoded";
    }

    private function base64UrlEncode(string $text): string
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }
    public function decode(string $client_token, string $secret): bool
    {
        $parts = explode('.', $client_token);
        if (count($parts) !== 3) {
            return false;
        }
        $signature = hash_hmac('sha256', "$parts[0].$parts[1]", $secret, true);
        $token_signature = $this->base64UrlDecode($parts[2]);

        if (strlen($signature) !== strlen($token_signature)) {
            return false;
        }

        return hash_equals($signature, $token_signature);
    }

    private function base64UrlDecode(string $text): string
    {
        $padding = strlen($text) % 4;
        if ($padding > 0) {
            $text .= str_repeat('=', 4 - $padding);
        }
        return base64_decode(
            str_replace(
                ['-', '_'],
                ['+', '/'],
                $text
            )
        );
    }
}

