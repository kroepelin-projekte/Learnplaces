<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;

class VerifyQRCode
{
    public function endpoint(array $params, array $request_body)
    {
        global $DIC;
        $user_id = $DIC->user()->getId();
        Response::send(200, "NOT_IMPLEMENTED", []);
    }
}