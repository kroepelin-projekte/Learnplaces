<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use KPG\Learnplaces\api\Database\OAuthEntity;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;

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
        $redirect_uri = $this->urlsafe_base64_decode($redirect_uri);
        $code_challenge = $query->retrieve('code_challenge', $string);

        $client_url = Settings::getClientURL();

        if (!str_contains($redirect_uri, $client_url)) {
            Response::send(400, 'BAD_REQUEST', []);
        }

        (new OAuthEntity())
            ->setState($state)
            ->setRedirectUri($redirect_uri)
            ->setCodeChallenge($code_challenge)
            ->setExpire(time() + 300)
            ->store();

        $base_url = strstr(ILIAS_HTTP_PATH, '/api', true);
        Header("Location: $base_url/goto.php?target=xsrl_lernorte-auth_$state");
        exit;
    }

    /**
     * @param $input
     * @return false|string
     */
    private function urlsafe_base64_decode($input) {
        $replaced = str_replace(['-', '_'], ['+', '/'], $input);

        $padding = strlen($replaced) % 4;
        if ($padding > 0) {
            $replaced .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($replaced);
    }
}