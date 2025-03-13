<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Authenticator;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Logout
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;
        $user_id = $DIC->user()->getId();
        Authenticator::destroyCookieByUserID($user_id);
        Response::send(200, NULL, ["logout" => "success"]);
    }
}