<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;

class GetResources
{
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;

        $identifier = htmlspecialchars($params['rid']);

        if ($DIC->resourceStorage()->manage()->find($identifier)) {
            $DIC->resourceStorage()->consume()->download(new ResourceIdentification($identifier))->run();
            Response::send(200);
        }
        Response::send(400, 'RESSOURCE_NOT_FOUND', []);
    }
}