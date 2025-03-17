<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator;

use ilAuthFrontendCredentials;
use ilAuthProviderFactory;
use ilAuthStatus;
use ilAuthFrontendFactory;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use Random\RandomException;
use ILIAS\HTTP\Response\ResponseHeader;
use ILIAS\Filesystem\Stream\Streams;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use ilLoggerFactory;

class Authenticator
{
    /**
     * @throws RandomException
     */
    public function auth(): array
    {
        error_log('auth started');

        $logger = ilLoggerFactory::getLogger('api___');
        $logger->info('start auth');

        if (array_key_exists('PHP_AUTH_USER', $_SERVER) or array_key_exists('PHP_AUTH_PW', $_SERVER)) {
            $logger->info('basic auth');

            if ($this->basicAuth()) {
                 $this->createToken();

                return [true, "basic_auth"];
            } else {
                return [false, "basic_auth"];
            }
        } elseif ($this->tokenAuth()) {
            $logger->info('token auth');

            $this->createToken();
            return [true, "token_auth"];
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
        $request_header = getallheaders();

        $logger = ilLoggerFactory::getLogger('api___');
        $logger->info(implode(', ', $request_header));


        if (!isset($request_header['Authorization'])) {
            Response::send(401, 'AUTH_ERROR_NO_BEARER_TOKEN');
        }
        $bearer_token = $request_header['Authorization'];
        if (!str_starts_with($bearer_token, 'Bearer ')) {
            Response::send(401, 'AUTH_ERROR_INVALID_BEARER_TOKEN');
        }
        $token = substr($bearer_token, 7);

        $token_handler = new TokenHandler();

        if (!$user_id = $token_handler->decode($token)) {
            Response::send(401, 'AUTH_ERROR_INVALID_JWT');
            return false;
        }
        global $DIC;
        $DIC->user()->setId($user_id);
        return true;
    }

    /**
     * @throws RandomException
     */
    private function createToken(): void
    {
        global $DIC;
        $token_handler = new TokenHandler();
        $userPayload['username'] = $DIC->user()->getlogin();
        $userPayload['sub'] = $DIC->user()->getId();
        $secret = Settings::getSecret();

        $userPayload['iat'] = time();
        $userPayload['exp'] = time() + Settings::getCookieExpire() * 60 * 60;

       // header('Access-Control-Expose-Headers: Learnplaces_token');
        header("Learnplaces_token: ". $token_handler->encode($userPayload, $secret));

    }

    /**
     * @throws ResponseSendingException
     */
    public function httpOptions(): void
    {
        error_log('httpOptions started');


        $client_url = Settings::getClientURL() ?: Settings::getBaseUrl();
        header("Access-Control-Allow-Origin: $client_url");
        header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Learnplaces_token");
        header('Access-Control-Expose-Headers: Learnplaces_token');


        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}