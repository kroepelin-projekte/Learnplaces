<?php

chdir("../../../../../../../../");

use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Authenticator;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\api\Core\Request;

require_once 'vendor/composer/vendor/autoload.php';

if (!file_exists('./ilias.ini.php')) {
    die('The ILIAS setup is not completed. Please run the setup routine.');
}
error_log("Wir starten in das Try");
try {
    $ilIliasIniFile = new ilIniFile('./ilias.ini.php');
    $ilIliasIniFile->read();
    ilInitialisation::initILIAS();
    $obj_authenticator = new Authenticator();
    $obj_authenticator->httpOptions();
    $auth_status = $obj_authenticator->auth();
    if ($auth_status[0]) {
        $request = new Request();
        $request->route($auth_status[1]);
    } else {
        Response::send(401);
    }
    echo $auth_status[1];
} catch (Exception $e) {
    ilLoggerFactory::getLogger('LPRestIntegration')->error('LEARN_PLACES_REST: ' . $e->getMessage());
    // Bevor wir live gehen, muss die nächste Zeile raus
    echo $e->getMessage();
    Response::serverError();
}