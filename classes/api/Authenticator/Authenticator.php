<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator;

use ilAuthFrontendCredentials;
use ilAuthProviderFactory;
use ilAuthStatus;
use ilAuthFrontendFactory;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use KPG\Learnplaces\api\Database\Tables\CookieSecrets;
use Random\RandomException;
use ILIAS\HTTP\Response\ResponseHeader;
use ILIAS\Filesystem\Stream\Streams;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Authenticator
{
    public const TOKEN_COOKIE_NAME = "learnplaces_token_auth_cookie";

    /**
     * @throws RandomException
     */
    public function auth(): array
    {
        if (array_key_exists('PHP_AUTH_USER', $_SERVER) or array_key_exists('PHP_AUTH_PW', $_SERVER)) {
            if ($this->basicAuth()) {
                $jwt = $this->createSessionToken();
                return [true, "basic_auth", $jwt];
            } else {
                return [false, "basic_auth"];
            }
        } elseif ($this->tokenAuth()) {
            $jwt = $this->createSessionToken();
            return [true, "token_auth", $jwt];
        } else {
            return [false, "token_auth"];
        }
    }

    /**
     * @return bool
     * @throws ResponseSendingException
     */
    private function basicAuth(): bool
    {
        $credentials = new ilAuthFrontendCredentials();
        $credentials->setUsername(htmlspecialchars($_SERVER['PHP_AUTH_USER']));
        $credentials->setPassword(htmlspecialchars($_SERVER['PHP_AUTH_PW']));
        $provider_factory = new ilAuthProviderFactory();
        $providers = $provider_factory->getProviders($credentials);

        $status = ilAuthStatus::getInstance();

        $frontend_factory = new ilAuthFrontendFactory();
        $frontend_factory->setContext(ilAuthFrontendFactory::CONTEXT_CLI);
        $frontend = $frontend_factory->getFrontend(
            $GLOBALS['DIC']['ilAuthSession'],
            $status,
            $credentials,
            $providers
        );
        $frontend->authenticate();

        switch ($status->getStatus()) {
            case ilAuthStatus::STATUS_AUTHENTICATED:
                return $this->checkRolePermission();
                break;
            default:
                Response::send(401, 'AUTH_ERROR');
                break;
        }
        return false;
    }

    /**
     * @return bool
     * @throws ResponseSendingException
     */
    private function checkRolePermission(): bool
    {
        global $DIC;
        $user_roles = $DIC->rbac()->review()->assignedGlobalRoles($DIC->user()->getId());
        if (in_array(SYSTEM_ROLE_ID, $user_roles)) {
            return true;
        }

        $perrmission_roles = explode(",", Settings::getPermissionRoles());
        foreach ($user_roles as $role) {
            if (in_array($role, $perrmission_roles)) {
                return true;
            }
        }
        Response::send(401, 'AUTH_ERROR');
        return false;
    }

    /**
     * @return bool
     * @throws ResponseSendingException
     */
    private function tokenAuth(): bool
    {
        global $DIC;
        if (!isset($_COOKIE[self::TOKEN_COOKIE_NAME])) {
            Response::send(401, 'AUTH_ERROR_NO_COOKIE');
            return false;
        }
        $token_handler = new TokenHandler();
        $jwt = $_COOKIE[self::TOKEN_COOKIE_NAME];

        if (!$user_id = $token_handler->decode($jwt)) {
            Response::send(401, 'AUTH_ERROR_INVALID_TOKEN');
            return false;
        }

        $DIC->user()->setId($user_id);
        return true;
    }

    /**
     * @throws RandomException
     */
    private function createSessionToken(): string
    {
        global $DIC;
        $token_handler = new TokenHandler();
        $userPayload['username'] = $DIC->user()->getlogin();
        $userPayload['sub'] = $DIC->user()->getId();
        $secret = Settings::getSecret();

        $userPayload['iat'] = time();
        $userPayload['exp'] = time() + Settings::getCookieExpire() * 60 * 60;
        $json_web_token = $token_handler->encode($userPayload, $secret);

        return $json_web_token;
    }

    /**
     * @throws ResponseSendingException
     */
    public function httpOptions(): void
    {
        $client_url = Settings::getClientURL() ?: Settings::getBaseUrl();
        header("Access-Control-Allow-Origin: $client_url");
        header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With");
        header("Access-Control-Allow-Credentials: true");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}