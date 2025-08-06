<?php

declare(strict_types=1);

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

        $logger = ilLoggerFactory::getLogger('Learnplaces xsrlAuthGUI');

        $http_handler = new HTTPHandler();
        if(!$http_handler->setState()) {
            $logger->error("State is not set.");
            Response::send(400, null, ['success' => false, 'access_token' => null]);
        }

        if ($DIC->user()->isAnonymous()) {
            $logger->error("User is not logged in. Redirect to login.php with target.");
            $http_handler->redirectLogin();
        }

        switch ($DIC->ctrl()->getCmd()) {
            case self::CMD_AUTH:
                (new PKCEHandler($http_handler))->initAfterILIASAuth();
                break;
        }
    }
}