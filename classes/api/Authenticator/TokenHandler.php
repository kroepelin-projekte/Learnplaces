<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator;

use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;

class TokenHandler
{
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

    public function createToken(): void
    {
        global $DIC;
        $token_handler = new TokenHandler();
        $userPayload['sub'] = $DIC->user()->getId();
        $logger = \ilLoggerFactory::getLogger('api___');
        $logger->info("User ID bei der Erstellung des auth Tokens: " . $userPayload['sub'] );
        $secret = Settings::getSecret();

        $userPayload['iat'] = time();
        $userPayload['exp'] = time() + Settings::getCookieExpire() * 60 * 60;


        header("Learnplaces_token: ". $token_handler->encode($userPayload, $secret));
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
     * @return mixed
     */
    public function decode(string $client_token): mixed
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

