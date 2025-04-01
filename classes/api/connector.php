<?php

chdir("../../../../../../../../");

use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Authenticator;
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\api\Core\Request;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;

require_once 'vendor/composer/vendor/autoload.php';

if (!file_exists('./ilias.ini.php')) {
    die('The ILIAS setup is not completed. Please run the setup routine.');
}

try {
    $ilIliasIniFile = new ilIniFile('./ilias.ini.php');
    $ilIliasIniFile->read();

    ilInitialisation::initILIAS();

    $client_url = Settings::getClientURL() ?: $_SERVER['HTTP_HOST'];
    header("Access-Control-Allow-Origin: $client_url");
    header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Learnplaces_token");
    header('Access-Control-Expose-Headers: Learnplaces_token');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $request = new Request();
    $request->route();

} catch (Exception $e) {
    echo $e->getMessage();
    Response::serverError();
}