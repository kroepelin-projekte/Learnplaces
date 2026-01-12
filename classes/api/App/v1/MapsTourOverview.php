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

        /** @var LearnplaceRepository $learnplace_service  */
        $learnplace_service = PluginContainer::resolve(LearnplaceRepository::class);

        $map_data = [];
        foreach ($learnplaces_map_plugin->getTourModel()->getTourMapsOfUser() as $tour) {
            $map_data[$tour['context_ref_id']] = [
                'map_id' => $tour['map_id'],
                "title" => $tour['title'],
                "context_ref_id" => $tour['context_ref_id'],
            ];

            foreach ($tour['tour_learnplaces'] as $learnplace ) {
                $learnplace_ref_id = $learnplace['learnplace_ref_id'];
                $visited = $learnplace['visited'];
                $learnplace_object = $learnplace_service->findByObjectId(\ilObject::_lookupObjId($learnplace_ref_id));

                $map_data[$tour['context_ref_id']]['tour_learnplaces'][] = [
                    'id' => $learnplace_object->getId(),
                    "learnplace_ref_id" => $learnplace_ref_id,
                    'title' => \ilObject::_lookupTitle($learnplace_object->getObjectId()),
                    'latitude' => $learnplace_object->getLocation()->getLatitude(),
                    'longitude' => $learnplace_object->getLocation()->getLongitude(),
                    'radius' => $learnplace_object->getLocation()->getRadius(),
                    'visited' => $visited,
                ];
            }
        }

        Response::send(200, null, $map_data);
    }
}