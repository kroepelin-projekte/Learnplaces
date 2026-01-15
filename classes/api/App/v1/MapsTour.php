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

        $tour = $learnplaces_map_plugin->getTourModel()->getTourMap($id);

        if ($tour) {
            $map_data = [
                'map_id' => $tour['map_id'],
                "title" => $tour['title'],
                "description" => $tour['description'],
                "context_ref_id" => $tour['context_ref_id'],
            ];

            foreach ($tour['tour_learnplaces'] ?? [] as $learnplace_ref_id ) {
                $learnplace_object = $learnplace_service->findByObjectId(\ilObject::_lookupObjId($learnplace_ref_id));

                /** @var Configuration $configuration */
                $configuration = $learnplace_object->getConfiguration();
                if (!$configuration->isOnline()) {
                    continue;
                }

                $map_data['tour_learnplaces'][] = [
                    'id' => $learnplace_object->getId(),
                    "learnplace_ref_id" => $learnplace_ref_id,
                    'title' => \ilObject::_lookupTitle($learnplace_object->getObjectId()),
                    'latitude' => $learnplace_object->getLocation()->getLatitude(),
                    'longitude' => $learnplace_object->getLocation()->getLongitude(),
                    'radius' => $learnplace_object->getLocation()->getRadius(),
                    'visited' => $learnplaces_map_plugin->getTourModel()->isVisited($DIC->user()->getId(), $learnplace_object->getId()),
                ];
            }

            Response::send(200, null, $map_data);
        }

        Response::send(404, null, ['error' => 'Tour Map not found']);
    }
}