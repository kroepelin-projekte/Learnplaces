<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use KPG\Learnplaces\persistence\dto\Location;
use Kpg\Plugins\LearnplacesMap\PageEditor\Tour\TourModel;

class MapsCollection
{
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

        $db = $DIC->database();
        $res = $db->queryF(
            <<<SQL
            SELECT m.id, m.context_ref_id, m.title, m.description
            FROM kpg_lmap_map AS m
            WHERE m.id = %s AND m.mode = 'collection'
            ORDER BY m.id ASC
            SQL,
            ['integer'],
             [$map_id]
        );

        if (!$row = $db->fetchAssoc($res)) {
            Response::send(200, null, []);
        }

        $collection_data = [
            'map_id' => (int) $row['id'],
            'context_ref_id' => (int) $row['context_ref_id'],
            'title' => $row['title'],
            'description' => nl2br($row['description']),
            'collection_learnplaces' => []
        ];

        $learnplaces = $learnplaces_map_plugin->getCollectionModel()->getLearnplacesOfCollection($map_id);
        foreach ($learnplaces as $learnplace_item) {
            $learnplace_ref_id = $learnplace_item['ref_id'];
            $learnplace = $learnplace_item['object'];
            /** @var Location $location */
            $location = $learnplace->getLocation();

            // Get visited status of current user
            $tour_model = new TourModel($DIC);
            $is_visited = $tour_model->isVisited($DIC->user()->getId(), $learnplace->getId());

            $collection_data['collection_learnplaces'][] = [
                'id' => $learnplace->getId(),
                'title' => \ilObject::_lookupTitle($learnplace->getObjectId()),
                'latitude' => $location->getLatitude(),
                'longitude' => $location->getLongitude(),
                'radius' => $location->getRadius(),
                'visited' => $is_visited ? 'true' : 'false',
                'url' => ILIAS_HTTP_PATH . '/go/xsrl/' . $learnplace_ref_id,
                'color' => $learnplace_item['color'],
                'tag_name' => $learnplace_item['tag_name'],
                'render_index' => $learnplace_item['render_index'],
            ];
        }

        // todo check assignment: $collection_data['map_id']

        Response::send(200, null, $collection_data);
    }
}