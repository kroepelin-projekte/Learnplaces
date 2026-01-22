<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Health
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        Response::send(200, null, []);
    }
    public function refresh(array $params, array $request_body): void
    {
        Response::send(200, null, []);
    }
}
