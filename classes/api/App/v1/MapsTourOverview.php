<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;

class MapsTourOverview
{
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;

        // Check if Learnplaces Map Plugin is active
        /** @var \ilLearnplacesMapPlugin $learnplaces_map_plugin */
        $learnplaces_map_plugin = \ilObjectPlugin::getPluginObjectByType('lmap');
        if (!$learnplaces_map_plugin || !$learnplaces_map_plugin->isActive()) {
            Response::send(200, null, []);
        }

        $tour_map_data = iterator_to_array($learnplaces_map_plugin->getTourModel()->getTourMapsOfUser());

        Response::send(200, null, $tour_map_data);
    }
}