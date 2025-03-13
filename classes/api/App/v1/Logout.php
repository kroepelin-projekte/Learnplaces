<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Authenticator;

class Logout
{
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;
        $user_id = $DIC->user()->getId();
        Authenticator::destroyCookieByUserID($user_id);
        Response::send();
    }
}