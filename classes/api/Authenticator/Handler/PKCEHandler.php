<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\api\Database\OAuthEntity;
use KPG\Learnplaces\api\Authenticator\Handler\PKCEUtilHandler;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use JetBrains\PhpStorm\NoReturn;

class PKCEHandler
{
    private HTTPHandler $http_handler;
    private PKCEUtilHandler $pkce_util;

    public function __construct(?HTTPHandler $http_handler = null)
    {
        $this->http_handler = $http_handler ?? new HTTPHandler();
        $this->pkce_util = new PKCEUtilHandler();
    }

    public function initBeforeILIASAuth(): void
    {
        if (!$this->http_handler->setRedirectUri()
            || !$this->http_handler->setCodeChallenge()
            || !$this->http_handler->setState()
        ) {
            Response::send(401, DEVMODE ? 'missing http_handler values' : '', ['success' => false]);
        }
        (new OAuthEntity())
            ->setState($this->http_handler->getState())
            ->setRedirectUri($this->http_handler->getRedirectUri())
            ->setCodeChallenge($this->http_handler->getCodeChallenge())
            ->setExpire(time() + 300)
            ->setCode(null)
            ->store();
        $this->http_handler->redirectTargetAuthGUI();
    }

    #[NoReturn]
    public function initAfterILIASAuth(): void
    {
        $record = OAuthEntity::where(['state' => $this->http_handler->getState()])->first();
        if (!$record) {
            $this->http_handler->redirectTarget(Settings::getClientURL());
        }

        $redirect_uri = $record->getRedirectUri();

        if (time() > $record->getExpire()) {
            $record->delete();
            $this->http_handler->redirectTarget($redirect_uri);
            exit;
        }
        $code = $this->pkce_util->generateCode();
        global $DIC;
        $record->setCode($code)->setUserId($DIC->user()->getId())->update();

        $this->http_handler->redirectTarget("$redirect_uri?code=$code&state=" . $this->http_handler->getState());

        exit;
    }

    public function initTokenAuth(): void
    {
        $logger = \ilLoggerFactory::getLogger('Learnplaces');

        $record = OAuthEntity::where(['state' => $this->http_handler->getState()])->first();
        if (!$record) {
            $logger->info('no record found for state');
            Response::send(401, null, ['success' => false, 'access_token' => null]);
        }
        if (time() > $record->getExpire()) {
            $logger->info('auth token expired');
            $record->delete();
            Response::send(401, null, ['success' => false, 'access_token' => null]);
        }
        if ($record->getCode() !== $this->http_handler->getCode()) {
            $logger->info('code not equal to code in db');
            $record->delete();
            Response::send(401, null, ['success' => false, 'access_token' => null]);
        }
        $code_verifier_hash = $this->pkce_util->base64UrlEncode(
            $this->pkce_util->hash($this->http_handler->getCodeVerifier())
        );
        if (!$this->pkce_util->hash_equals($code_verifier_hash, $record->getCodeChallenge())) {
            $logger->info('hash not equal to code challenge in db');
            $record->delete();
            Response::send(401, null, ['success' => false, 'access_token' => null]);
        }
        global $DIC;
        $DIC->user()->setId($record->getUserId());
        $record->delete();
        header("Learnplaces_token: " . $this->pkce_util->createAccessToken());
    }

    public function initAccessTokenAuth(): bool|string|int
    {
        if (!$user_id = $this->pkce_util->decodeAccessToken($this->http_handler->getAccessToken())) {
            return false;
        }
        global $DIC;
        $DIC->user()->setId($user_id);

        $record = OAuthEntity::get();
        foreach ($record as $r) {
            if (time() > $r->getExpire()) {
                $r->delete();
            }
        }

        header("Learnplaces_token: " . $this->pkce_util->createAccessToken());

        return true;
    }

}
