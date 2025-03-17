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
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Authenticator
{
    public const TOKEN_COOKIE_NAME = "learnplaces_token_auth_cookie";

    /**
     * @throws RandomException|ResponseSendingException
     */
    public function auth(): array
    {
        if (array_key_exists('PHP_AUTH_USER', $_SERVER) or array_key_exists('PHP_AUTH_PW', $_SERVER)) {
            if ($this->basicAuth()) {
                $secret = $this->createSessionToken();
                //$this->refreshILIASCookie();
                $this->addSecretToDatabase($secret);
                return [true, "basic_auth"];
            } else {
                return [false, "basic_auth"];
            }
        } else {
            if ($this->tokenAuth()) {
                $secret = $this->createSessionToken();
                $this->addSecretToDatabase($secret);
               // $this->refreshILIASCookie();
                return [true, "token_auth"];
            } else {
                return [false, "token_auth"];
            }
        }
    }

    /**
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

    private function tokenAuth(): bool
    {
        global $DIC;
        if (!isset($_COOKIE[self::TOKEN_COOKIE_NAME])) {
            Response::send(401, 'AUTH_ERROR_NO_COOKIE');
            return false;
        }
        $token_handler = new TokenHandler();
        $client_token = $_COOKIE[self::TOKEN_COOKIE_NAME];
        $all_secrets = CookieSecrets::getAll();
        $auth = false;
        $auth_secret = [];
        foreach ($all_secrets as $secret) {
            if ($token_handler->decode($client_token, $secret['secret'])) {
                $auth = true;
                $auth_secret = $secret;
                break;
            }
        }
        if (!$auth) {
            Response::send(401, 'AUTH_ERROR_INVALID_TOKEN');
            return false;
        }

        $expiration_timestamp = strtotime($auth_secret['updated_at']) + Settings::getCookieExpire() * 60 * 60;
        $current_time = time();
        if ($current_time > $expiration_timestamp) {
            $this->destroyCookieByID($auth_secret['id']);
            Response::send(401, 'AUTH_ERROR_TOKEN_EXPIRED');
            return false;
        }
        $DIC->user()->setId($auth_secret['user_id']);
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
        $secret = $token_handler->createSecret();

        $userPayload['iat'] = time();
        $userPayload['exp'] = time() + Settings::getCookieExpire() * 60 * 60;
        $json_web_token = $token_handler->encode($userPayload, $secret);
        $cookieOptions = [
            'expires' => $userPayload['exp'],
            'domain' => '.' . Settings::getBaseURL(),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None',
        ];
        setcookie(self::TOKEN_COOKIE_NAME, $json_web_token, $cookieOptions);
        return $secret;
    }

    private function addSecretToDatabase(string $secret): void
    {
        global $DIC;
        CookieSecrets::updateOrInsertSecret($secret, $DIC->user()->getId());
    }

    private function destroyCookieByID(int $id): void
    {
        setcookie(self::TOKEN_COOKIE_NAME, '', time() - 3600);
        CookieSecrets::deleteSecretByID($id);
    }

    public static function destroyCookieByUserID(int $user_id): void
    {
        setcookie(self::TOKEN_COOKIE_NAME, '', time() - 3600);
        CookieSecrets::deleteSecretByUserID($user_id);
    }

    public function httpOptions(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        ini_set('session.use_cookies', 0);
        ini_set('session.use_trans_sid', 0);

        $client_url = Settings::getClientURL() ?: Settings::getBaseUrl();

        header("Access-Control-Allow-Origin: https://learnplaces.kroepelin-projekte.de");
        header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With");
        header("Access-Control-Allow-Credentials: true");

        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    private function refreshILIASCookie(): void
    {

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        $cookieParams = session_get_cookie_params();

        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path' => $cookieParams['path'],
            'domain' => $cookieParams['domain'],
            'secure' => $cookieParams['secure'],
            'httponly' => $cookieParams['httponly'],
            'samesite' => 'None',
        ]);
        session_start();
    }
}