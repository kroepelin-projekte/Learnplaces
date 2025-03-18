<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator;

use ilAuthFrontendCredentials;
use ilAuthProviderFactory;
use ilAuthStatus;
use ilAuthFrontendFactory;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Authenticator
{
    private TokenHandler $tokenHandler;

    public function __construct()
    {
        $this->tokenHandler = new TokenHandler();
    }

    /**
     * @throws ResponseSendingException
     */
    public function auth(): array
    {
        if (isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) && $this->basicAuth()) {
            return ['success' => true, "auth_mode" => "basic_auth"];
        }

        if ($this->tokenAuth()) {
            return ['success' => true, "auth_mode" => "token_auth"];
        }

        return ['success' => false, "auth_mode" => "none"];
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
                if ($this->checkRolePermission()) {
                    $this->tokenHandler->createToken();
                    return true;
                } else {
                    Response::send(401, 'AUTH_ERROR');
                    return false;
                }
                break;
            default:
                Response::send(401, 'AUTH_ERROR');
                return false;
                break;
        }
    }

    /**
     * @return bool
     * @throws ResponseSendingException
     */
    private function tokenAuth(): bool
    {
        $request_header = getallheaders();
        if (!isset($request_header['Authorization'])) {
            Response::send(401, 'AUTH_ERROR_NO_BEARER_TOKEN');
            return false;
        }
        $bearer_token = $request_header['Authorization'];
        if (!str_starts_with($bearer_token, 'Bearer ')) {
            Response::send(401, 'AUTH_ERROR_INVALID_BEARER_TOKEN');
            return false;
        }
        $token = substr($bearer_token, 7);

        $token_handler = new TokenHandler();

        if (!$user_id = $token_handler->decode($token)) {
            Response::send(401, 'AUTH_ERROR_INVALID_JWT');
            return false;
        }
        $logger = \ilLoggerFactory::getLogger('api___');
        $logger->info("User ID nach dem Token auth: " . $user_id);
        $this->tokenHandler->createToken();
        global $DIC;
        $DIC->user()->setId($user_id);

        return true;
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
}