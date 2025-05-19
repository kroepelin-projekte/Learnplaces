<?php
use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\api\Core\Request;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use KPG\Learnplaces\api\IliasInit;

require_once('IliasInit.php');
IliasInit::init();
try {
    $client_url = Settings::getClientURL() ?: $_SERVER['HTTP_HOST'];
    header("Access-Control-Allow-Origin: $client_url");
    header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Learnplaces_token");
    header('Access-Control-Expose-Headers: Learnplaces_token');
    header('Cache-Control: public, max-age=31536000'); // Cache für 1 Jahr (31536000 Sekunden)

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $request = new Request();
    $request->route();

} catch (\Exception $e) {
    echo $e->getMessage();
    //Response::serverError();
}
exit;