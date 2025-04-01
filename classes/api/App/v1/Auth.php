<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use KPG\Learnplaces\api\Database\OAuthEntity;

class Auth
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;
        $query = $DIC->http()->wrapper()->query();
        $string = $DIC->refinery()->kindlyTo()->string();

        if (! $query->has('redirect_uri')
            || !$query->has('code_challenge')
            || !$query->has('state')
        ) {
            Response::send(400, 'BAD_REQUEST', []);
        }
        $state = $query->retrieve('state', $string);
        $redirect_uri = $query->retrieve('redirect_uri', $string);
        $code_challenge = $query->retrieve('code_challenge', $string);

        (new OAuthEntity())
            ->setState($state)
            ->setRedirectUri($redirect_uri)
            ->setCodeChallenge($code_challenge)
            ->setExpire(time() + 300)
            ->store();


        // todo validierung: redirect_uri muss unter definierten erlaubten uris sein

        // todo parameter (redirect_uri, code_challenge) zwischespeichern 5min

        $base_url = strstr(ILIAS_HTTP_PATH, '/api', true);
        Header("Location: $base_url/goto.php?target=xsrl_lernorte-auth_$state");
        exit;
    }
}