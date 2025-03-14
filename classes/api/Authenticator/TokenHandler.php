<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator;

class TokenHandler
{
    public function createSecret(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * @param array  $payload
     * @param string $secret
     * @return string
     */
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

    /**
     * @param string $text
     * @return string
     */
    private function base64UrlEncode(string $text): string
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    /**
     * @param string $client_token
     * @param string $secret
     * @return bool
     */
    public function decode(string $client_token, string $secret): bool
    {
        if (preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/", $client_token, $matches) !== 1) {
            return false;
        }

        $signature = hash_hmac(
            'sha256',
            $matches['header'] . '.' . $matches['payload'],
            $secret,
            true
        );

        $token_signature = $this->base64UrlDecode($matches['signature']);

        if (!hash_equals($signature, $token_signature)) {
            return false;
        }

        $payload = json_decode($this->base64UrlDecode($matches['payload']), true);

        if (!is_array($payload) || !isset($payload['sub'], $payload['exp'])) {
            return false;
        }

        if (!is_int($payload['exp']) || $payload['exp'] <= 0) {
            return false;
        }

        if (!is_int($payload['sub']) || empty($payload['sub'])) {
            return false;
        }

        if (time() >= $payload['exp']) {
            return false;
        }

        return true;
    }

    /**
     * @param string $text
     * @return string
     */
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

