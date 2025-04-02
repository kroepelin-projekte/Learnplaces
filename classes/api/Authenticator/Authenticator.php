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
        $request_header = getallheaders();
        if (!isset($request_header['Authorization'])) {
            Response::send(401, null, ['success' => false]);
            return false;
        }
        $bearer_token = $request_header['Authorization'];
        if (!str_starts_with($bearer_token, 'Bearer ')) {
            Response::send(401, null, ['success' => false]);
            return false;
        }
        $http_handler = new HTTPHandler();
        $http_handler->setAccessToken(substr($bearer_token, 7));
        $pkce_handler = new PKCEHandler($http_handler);
        if(!$pkce_handler->initAccessTokenAuth()){
            Response::send(401, null, ['success' => false]);
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
        Response::send(401, null, ['success' => false]);
        return false;
    }
}