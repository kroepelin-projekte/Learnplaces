<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Token
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        // todo validierung:
        //  hash_equals mit code_challenge vom Zwischenspeicher und code_verifier vom Parameter
        //  state abgleich
        //  code abgleich

        // todo zwischenspeicher löschen

        // todo jwt als response schicken
        $jwt = 'xyz';

        Response::send(201, null, ['access_token' => $jwt]);
    }
}