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
    $auth = new Authenticator();
    $auth_status = $auth->auth();
    if ($auth_status[0]) {
        $request = new Request();
        $request->route($auth_status[1]);
    } else {
        Response::send(401);
    }
    echo $auth_status[1];
} catch (Exception $e) {
    // Logger einbauen
    echo $e->getMessage();
}