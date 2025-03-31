<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Auth
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        // todo validierung: redirect_uri muss unter definierten erlaubten uris sein

        // todo parameter (state, redirect_uri, code_challenge) zwischespeichern 5min

        $base_url = strstr(ILIAS_HTTP_PATH, '/api', true);
        Header("Location: $base_url/goto.php?target=xsrl_lernorte-auth");
        exit;
    }
}