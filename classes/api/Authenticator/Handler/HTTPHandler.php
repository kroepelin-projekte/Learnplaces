<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler;

use ILIAS\HTTP\Wrapper\ArrayBasedRequestWrapper;
use ILIAS\Refinery\Transformation;

class HTTPHandler
{
    private ?string $redirectUri = null;
    private ?string $state = null;
    private ?string $code_challenge = null;
    private ArrayBasedRequestWrapper $query;
    private Transformation $string;

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
        $this->redirectUri = $this->query->retrieve('redirect_uri', $this->string);
        return $this->verifyRedirectUri();
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    private function verifyRedirectUri(): bool
    {
        # ToDo prüfen mit Settings
        return true;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(): bool
    {
        if (!$this->query->has('state')) {
            return false;
        }
        $this->state = $this->query->retrieve('state', $this->string);
        return true;
    }

    public function getCodeChallenge(): ?string
    {
        return $this->code_challenge;
    }

    public function setCodeChallenge(): bool
    {
        if (!$this->query->has('code_challenge')) {
            return false;
        }
        $this->code_challenge = $this->query->retrieve('code_challenge', $this->string);
        return true;
    }

    public function redirectTargetAuthGUI(): void
    {
        $base_url = strstr(ILIAS_HTTP_PATH, '/api', true);
        $this->redirectTarget("Location: $base_url/goto.php?target=xsrl_lernorte-auth_$this->state");
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
}