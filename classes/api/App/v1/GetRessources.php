<?php
namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;

class GetRessources
{
    public function endpoint(array $params, array $request_body) {
        global $DIC;

        $identifier = $params['rid'];

        if ($DIC->resourceStorage()->manage()->find($identifier)) {
            $resource_identification = new ResourceIdentification($identifier);
            $DIC->resourceStorage()->consume()->download($resource_identification)->run();
            Response::send(200);
        }
        Response::send(400, 'RESSOURCE_NOT_FOUND', []);
    }
}