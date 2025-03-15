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

        if ($DIC->resourceStorage()->manage()->find($rid)) {
            // full download
            $DIC->resourceStorage()->consume()->download(new ResourceIdentification($rid))->run();
            Response::send(200);

            // download of src url (nor working for video)
/*            $src = $DIC->resourceStorage()->consume()->src(new ResourceIdentification($rid))->getSrc();
            $base_url = strstr(ILIAS_HTTP_PATH, '/api', true);
            $src = $base_url . strstr($src, '/deliver.php', false);
            Response::send(200, null, ['src' => $src]);*/
        }
        Response::send(400, 'RESSOURCE_NOT_FOUND', []);
    }
}