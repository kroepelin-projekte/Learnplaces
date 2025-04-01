<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use KPG\Learnplaces\api\Database\OAuthEntity;

class Token
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;


        if (!isset($request_body['state'], $request_body['code'], $request_body['code_verifier'])) {
            Response::send(400, 'BAD_REQUEST', []);
        }

        $state = $request_body['state'];
        $code = $request_body['code'];
        $code_verifier = $request_body['code_verifier'];

        $record = OAuthEntity::where(['state' => $state])->first();
        if (!$record) {
            Response::send(400, 'BAD_REQUEST', []);
        }

        if ($record->getCode() !== $code) {
            Response::send(400, 'BAD_REQUEST', []);
        }

        $code_challenge = $record->getCodeChallenge();
        $code_verifier_hash = hash('sha256', $code_verifier, true);
        $hashedVerifier = $this->base64UrlEncode($code_verifier_hash); // Wichtig: raw_output = true
        if (!hash_equals($code_challenge, $hashedVerifier)) {
            Response::send(400, 'BAD_REQUEST', []);
        }


        // todo validierung:
        //  hash_equals mit code_challenge vom Zwischenspeicher und code_verifier vom Parameter
        //  state abgleich
        //  code abgleich

        // todo zwischenspeicher löschen

        // todo jwt als response schicken
        $jwt = 'xyz';

        Response::send(201, null, ['access_token' => $jwt]);
    }

    private function base64UrlEncode(string $text): string
    {
        // Konvertiere Rohdaten (binär) in einen Base64-String und mache ihn URL-sicher
        return rtrim(strtr(base64_encode($text), '+/', '-_'), '=');
    }

}