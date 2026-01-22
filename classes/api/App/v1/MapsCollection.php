<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class MapsCollection
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;

        if (!isset($params['id'])) {
            Response::send(400, null, ["error" => "Collection Map"]);
        }

        $map_id = $params['id'];

        // Check if Learnplaces Map Plugin is active
        /** @var \ilLearnplacesMapPlugin $learnplaces_map_plugin */
        $learnplaces_map_plugin = \ilObjectPlugin::getPluginObjectByType('lmap');
        if (!$learnplaces_map_plugin || !$learnplaces_map_plugin->isActive()) {
            Response::send(200, null, []);
        }

        $collection_data = $learnplaces_map_plugin->getCollectionModel()->getCollection($map_id);

        Response::send(200, null, $collection_data);
    }
}
