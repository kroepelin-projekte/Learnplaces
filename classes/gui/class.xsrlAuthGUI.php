<?php

declare(strict_types=1);

use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\security\AccessGuard;
use ILIAS\DI\UIServices;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\gui\VisitorsTable;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\api\Database\OAuthEntity;
use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler\HTTPHandler;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler\PKCEHandler;

/**
 * @ilCtrl_isCalledBy xsrlAuthGUI: ilUIPluginRouterGUI
 */
class xsrlAuthGUI
{
    public const CMD_AUTH = 'auth';

    /**
     * @return void
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function executeCommand(): void
    {
        global $DIC;

        $http_handler = new HTTPHandler();
        if(!$http_handler->setState()) {
            Response::send(400, 'BAD_REQUEST');
        }

        if ($DIC->user()->isAnonymous()) {
            $http_handler->redirectLogin();
        }

        switch ($DIC->ctrl()->getCmd()) {
            case self::CMD_AUTH:
                $this->auth($http_handler);
                break;
        }
    }

    /**
     * @return void
     */
    private function auth(HTTPHandler $http_handler): void
    {

        (new PKCEHandler($http_handler))->initAfterILIASAuth();




        $expire = $record->getExpire();

        $redirect_uri = $record->getRedirectUri();
        $redirect_uri = $this->urlsafe_base64_decode($redirect_uri);

        if (time() > $expire) {
            header("Location: $redirect_uri");
            exit;
        }

        $code = bin2hex(random_bytes(32));



        $uri = "$redirect_uri?code=$code&state=$state";

        header("Location: $uri");
        exit;
    }

    /**
     * @param $input
     * @return false|string
     */
    private function urlsafe_base64_decode($input)
    {
        $replaced = str_replace(['-', '_'], ['+', '/'], $input);

        $padding = strlen($replaced) % 4;
        if ($padding > 0) {
            $replaced .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($replaced);
    }
}