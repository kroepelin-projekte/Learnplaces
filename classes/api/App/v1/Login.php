<?php
namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;

class Login
{
    public function endpoint(array $params, array $request_body) {
        Response::send(201,NULL, ["login" => "success"]);
    }
}