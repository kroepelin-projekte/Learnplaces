<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator;

use ilAuthFrontendCredentials;
use ilAuthProviderFactory;
use ilAuthStatus;
use ilAuthFrontendFactory;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler\HTTPHandler;
use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler\PKCEHandler;

class Authenticator
{
    /**
     * @throws ResponseSendingException
     */
    public function auth(): bool
    {
        $logger = \ilLoggerFactory::getLogger('Learnplaces');

        $authorization_server_variable = $_SERVER['HTTP_AUTHORIZATION'] ?? 'not set';
        $logger->info('$_SERVER[\'authorization\']: ' . $authorization_server_variable);

        $request_headers = array_change_key_case(getallheaders(), CASE_LOWER);
        if (!isset($request_headers['authorization'])) {
            $logger->error('no authorization header variable found');
            Response::send(401, DEVMODE ? 'no header' : '', ['success' => false]);
            return false;
        }
        $bearer_token = $request_headers['authorization'];
        if (!str_starts_with($bearer_token, 'Bearer ')) {
            $logger->error('no bearer token found. authorization header: ' . $request_headers['authorization']);
            Response::send(401, DEVMODE ? 'no bearer token' : '', ['success' => false]);
            return false;
        }
        $logger->info('bearer token found');
        $http_handler = new HTTPHandler();
        $http_handler->setAccessToken(substr($bearer_token, 7));
        $pkce_handler = new PKCEHandler($http_handler);
        if(!$pkce_handler->initAccessTokenAuth()){
            $logger->error('bearer token not valid');
            Response::send(401, DEVMODE ? 'bearer token not valid' : '', ['success' => false]);
            return false;
        }

        return $this->checkRolePermission();
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

        $permissions_roles = explode(",", Settings::getPermissionRoles());
        foreach ($user_roles as $role) {
            if (in_array($role, $permissions_roles)) {
                return true;
            }
        }
        Response::send(401, DEVMODE ? 'no role permission' : '', ['success' => false]);
        return false;
    }
}