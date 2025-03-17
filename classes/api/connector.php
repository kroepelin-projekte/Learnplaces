<?php

chdir("../../../../../../../../");

use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Authenticator;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\api\Core\Request;

require_once 'vendor/composer/vendor/autoload.php';

if (!file_exists('./ilias.ini.php')) {
    die('The ILIAS setup is not completed. Please run the setup routine.');
}

try {

    $ilIliasIniFile = new ilIniFile('./ilias.ini.php');
    $ilIliasIniFile->read();

    ilInitialisation::initILIAS();

    $logger = ilLoggerFactory::getLogger('api___');
    $logger->info('api started');
    $logger->info($_SERVER['REQUEST_METHOD']);

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
    echo $e->getMessage();
    Response::serverError();
}