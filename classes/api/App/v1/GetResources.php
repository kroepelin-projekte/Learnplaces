<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class GetResources
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;

        $rid = htmlspecialchars($params['rid']);

        if ($identification = $DIC->resourceStorage()->manage()->find($rid)) {
            $DIC->resourceStorage()->consume()->download(new ResourceIdentification($rid))->run();
            Response::send(200);
        }
        Response::send(401, 'RESSOURCE_NOT_FOUND', []);
    }
}
