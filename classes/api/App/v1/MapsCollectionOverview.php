<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class MapsCollectionOverview
{
    /**
     * @throws ResponseSendingException
     * @throws \ilDatabaseException
     */
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;

        // Check if Learnplaces Map Plugin is active
        /** @var \ilLearnplacesMapPlugin $learnplaces_map_plugin */
        $learnplaces_map_plugin = \ilObjectPlugin::getPluginObjectByType('lmap');
        if (!$learnplaces_map_plugin || !$learnplaces_map_plugin->isActive()) {
            Response::send(200, null, []);
        }

        $map_data = iterator_to_array($learnplaces_map_plugin->getCollectionModel()->getCollectionsOfUser());

        Response::send(200, null, $map_data);
    }
}
