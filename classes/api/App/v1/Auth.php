<?php

namespace KPG\Learnplaces\api\App\v1;

use Repository\RepositoryObject\Learnplaces\classes\api\Authenticator\Handler\PKCEHandler;

class Auth
{

    public function endpoint(array $params, array $request_body): void
    {
        (new PkceHandler())->initBeforeILIASAuth();
    }
}