<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use KPG\Learnplaces\persistence\dto\Configuration;

class MapsTour
{
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;

        if (!isset($params['id'])) {
            Response::send(400, null, ["error" => "Tour Map"]);
        }

        $id = $params['id'];

        // Check if Learnplaces Map Plugin is active
        /** @var \ilLearnplacesMapPlugin $learnplaces_map_plugin */
        $learnplaces_map_plugin = \ilObjectPlugin::getPluginObjectByType('lmap');
        if (!$learnplaces_map_plugin || !$learnplaces_map_plugin->isActive()) {
            Response::send(200, null, []);
        }

        /** @var LearnplaceRepository $learnplace_service  */
        $learnplace_service = PluginContainer::resolve(LearnplaceRepository::class);

        $tour_map_data = $learnplaces_map_plugin->getTourModel()->getTourMap($id);

        if (!$tour_map_data) {
            Response::send(404, null, ['error' => 'Tour Map not found']);
        }

        Response::send(200, null, $tour_map_data);
    }
}