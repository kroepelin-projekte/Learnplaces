<?php

namespace KPG\Learnplaces\api\Authenticator\Handler;

use Random\RandomException;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;

class PKCEUtilHandler
{

    /**
     * @throws RandomException
     */
    public function generateCode(): string
    {
        return bin2hex(random_bytes(32));
    }



    public function base64UrlEncode(string $text): string
    {
        return rtrim(strtr(base64_encode($text), '+/', '-_'), '=');
    }

    public function hash(string $value): string
    {
        return hash('sha256', $value, true);
    }

    public function hash_equals($a, $b): bool
    {
        return hash_equals($a, $b);
    }

    public function createAccessToken(): string
    {
        global $DIC;
        $userPayload['sub'] = $DIC->user()->getId();
        $secret = Settings::getSecret();

        $userPayload['iat'] = time();
        $userPayload['exp'] = time() + Settings::getCookieExpire() * 60 * 60;
        return $this->encodeAccessToken($userPayload, $secret);
    }

    public function encodeAccessToken(array $payload, string $secret): string
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

    public function decodeAccessToken(string $client_token): mixed
    {
        if (preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/", $client_token, $matches) !== 1) {
            return false;
        }

        $secret = Settings::getSecret();

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

        return $payload['sub'];
    }

    public function base64UrlDecode(string $text): string
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