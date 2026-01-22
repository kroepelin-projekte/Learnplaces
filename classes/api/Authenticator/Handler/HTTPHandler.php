<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler;

use ILIAS\HTTP\Wrapper\ArrayBasedRequestWrapper;
use ILIAS\Refinery\Transformation;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;

class HTTPHandler
{
    private ?string $redirectUri = null;
    private ?string $state = null;
    private ?string $code_challenge = null;
    private ArrayBasedRequestWrapper $query;
    private Transformation $string;
    private string $code;
    private string $code_verifier;
    private string $access_token;

    public function __construct()
    {
        global $DIC;
        $this->query = $DIC->http()->wrapper()->query();
        $this->string = $DIC->refinery()->kindlyTo()->string();
    }

    public function setRedirectUri(): bool
    {
        if (!$this->query->has('redirect_uri')) {
            return false;
        }

        $this->redirectUri = $this->urlsafe_base64_decode($this->query->retrieve('redirect_uri', $this->string));
        return $this->verifyRedirectUri();
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    private function verifyRedirectUri(): bool
    {
        $parsed_client_url = parse_url($this->redirectUri);
        $base_client_url = $parsed_client_url['scheme'] . "://" . $parsed_client_url['host'];
        if (isset($parsed_client_url['port'])) {
            $base_client_url .= ":" . $parsed_client_url['port'];
        }

        return Settings::getClientURL() == $base_client_url;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state = null): bool
    {
        if ($state == null) {
            if (!$this->query->has('state')) {
                return false;
            }
            $this->state = $this->query->retrieve('state', $this->string);
        } else {
            $this->state = $state;
        }
        return true;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCodeVerifier(string $code): void
    {
        $this->code_verifier = $code;
    }

    public function getCodeVerifier(): string
    {
        return $this->code_verifier;
    }

    public function getCodeChallenge(): ?string
    {
        return $this->code_challenge;
    }

    public function setCodeChallenge(?string $code_challenge = null): bool
    {
        if ($code_challenge == null) {
            if (!$this->query->has('code_challenge')) {
                return false;
            }
            $this->code_challenge = $this->query->retrieve('code_challenge', $this->string);
        } else {
            $this->code_challenge = $code_challenge;
        }

        return true;
    }

    public function redirectTargetAuthGUI(): void
    {
        $base_url = strstr(ILIAS_HTTP_PATH, '/api', true);
        $this->redirectTarget("$base_url/goto.php?target=xsrl_lernorte-auth_$this->state");
        exit;
    }

    public function redirectLogin(): void
    {
        $target = 'xsrl_lernorte-auth_' . $this->getState();
        $this->redirectTarget('login.php?target=' . $target . '&cmd=force_login');
    }

    public function redirectTarget(string $target): void
    {
        global $DIC;
        $DIC->ctrl()->redirectToURL($target);
    }

    public function urlsafe_base64_decode($input): false|string
    {
        $replaced = str_replace(['-', '_'], ['+', '/'], $input);

        $padding = strlen($replaced) % 4;

        if ($padding > 0) {
            $replaced .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($replaced);
    }
    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public function setAccessToken(string $access_token): void
    {
        $this->access_token = $access_token;
    }
}
