<?php

namespace KPG\Learnplaces\api;

use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Authenticator;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\api\Core\Request;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use Random\RandomException;
use ilLoggerFactory;
use ilIniFile;
use ilInitialisation;
use Exception;

class connector
{
    public function __construct()
    {
        chdir("../../../../../../../../");
        require_once 'vendor/composer/vendor/autoload.php';

        try {
            if (!file_exists('./ilias.ini.php')) {
                ilLoggerFactory::getLogger('LPRestIntegration')->error(
                    'LEARN_PLACES_REST_ILIAS_INI: ' . "ILIAS_INI_FILE_NOT_FOUND"
                );
                Response::serverError();
            }
            $ilIliasIniFile = new ilIniFile('./ilias.ini.php');
            $ilIliasIniFile->read();
            ilInitialisation::initILIAS();
        } catch (Exception $e) {
            ilLoggerFactory::getLogger('LPRestIntegration')->error('LEARN_PLACES_REST_ILIAS_INI: ' . $e->getMessage());
            Response::serverError();
        }
    }

    /**
     * @throws ResponseSendingException
     */
    public function connect(): void
    {
        try {
            $obj_authenticator = new Authenticator();
            $obj_authenticator->httpOptions();
            $auth_status = $obj_authenticator->auth();
            if ($auth_status[0]) {
                $request = new Request();
                $request->route($auth_status[1]);
            } else {
                Response::send(401);
            }
        } catch (Exception $e) {
            ilLoggerFactory::getLogger('LPRestIntegration')->error('LEARN_PLACES_REST_CONNECT: ' . $e->getMessage());
            Response::serverError();
        }
    }
}

$connect = new connector();
$connect->connect();

