<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\api\Database\OAuthEntity;
use KPG\Learnplaces\api\Authenticator\Handler\PKCEUtilHandler;

class PKCEHandler
{
    private HTTPHandler $http_handler;
    private PKCEUtilHandler $pkce_util;

    public function __construct(?HTTPHandler $http_handler = null)
    {
        $this->http_handler = $http_handler ?? new HTTPHandler();
        $this->pkce_util = new PKCEUtilHandler();
    }

    public function initbeforeILIASAuth()
    {
        if (!$this->http_handler->setRedirectUri()
            || !$this->http_handler->setCodeChallenge()
            || !$this->http_handler->setState()
        ) {
            Response::send(400, 'BAD_REQUEST');
        }
        (new OAuthEntity())
            ->setState($this->http_handler->getState())
            ->setRedirectUri($this->http_handler->getRedirectUri())
            ->setCodeChallenge($this->http_handler->getCodeChallenge())
            ->setExpire(time() + 300)
            ->store();
        $this->http_handler->redirectTargetAuthGUI();
    }

    public function initAfterILIASAuth(): void
    {
        $record = OAuthEntity::where(['state' => $this->http_handler->getState()])->first();
        if (!$record) {
            #Weiterleitung auf client ??
            Response::send(400, 'BAD_REQUEST');
            throw new \Exception('Permission Denied');
        }

        $expire = $record->getExpire();

        $redirect_uri = $record->getRedirectUri();
        $redirect_uri = $this->urlsafe_base64_decode($redirect_uri);

        if (time() > $expire) {
            header("Location: $redirect_uri");
            exit;
        }
        $code = $this->pkce_util->generateCode();

        $record
            ->setCode($code)
            ->update();
        $uri = "$redirect_uri?code=$code&state=$this->http_handler->getState()";

        header("Location: $uri");
        exit;
    }

}