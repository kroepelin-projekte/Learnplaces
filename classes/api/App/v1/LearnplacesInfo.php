<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;

class LearnplacesInfo
{
    private array $removing_block_ids = [];
    public function endpoint(array $params, array $request_body)
    {
        global $ilDB;
        $id = $params['id'];

        $learn_places_repo = PluginContainer::resolve(LearnplaceRepository::class);
        try {
            $obj_learn_place = $learn_places_repo->find($id);
        } catch (\Exception $e) {
            Response::send(400, "LEARNPLACE_NOT_FOUND", []);
        }
        $learn_place_configuration = $obj_learn_place->getConfiguration();
        if (!$learn_place_configuration->isOnline()) {
            Response::send(400, "LEARNPLACE_NOT_FOUND", []);
        }

        Response::send(200, null, $this->getResponseArray($obj_learn_place, $learn_place_configuration));
    }

    private function getBlockArray($block): array
    {
        $block_array = [
            "id" => $block->getId(),
            "type" => basename(str_replace('\\', '/', get_class($block))),
            "sequence" => $block->getSequence(),
            "visible" => $block->getVisibility(),
            "constraints" => $block->getConstraint(),

        ];
        if (method_exists($block, 'getContent')) {
            $block_array['content'] = $block->getContent();
        }
        if (method_exists($block, 'getTitle')) {
            $block_array['title'] = $block->getTitle();
        }
        if (method_exists($block, 'isExpand')) {
            $block_array['expand'] = $block->isExpand();
        }
        if (method_exists($block, 'getDescription')) {
            $block_array['description'] = $block->getDescription();
        }
        if (method_exists($block, 'getPicture')) {
            $block_array['picture'] = $block->getPicture()->getResourceId();
        }
        if (method_exists($block, 'getRefId')) {
            $block_array['ilias_ref_id'] = $block->getRefId();
        }
        if(method_exists($block, 'getResourceId')) {
            $block_array['resource_id'] = $block->getResourceId();
        }

        if (method_exists($block, 'getBlocks')) {
            $sub_blocks = $block->getBlocks();
            $sub_block_array = [];
            foreach ($sub_blocks as $sub_block) {
                $this->removing_block_ids[] = $sub_block->getId();
                $sub_block_array[] = $this->getBlockArray($sub_block);
            }

            $block_array['sub_blocks'] = $sub_block_array;
        }

        return $block_array;
    }

    private function filterBlockArray(array $block_array, array $removing_ids): array
    {
        return array_filter($block_array, function ($block) use ($removing_ids) {
            return !in_array($block['id'], $removing_ids);
        });
    }
    private function orderBlockArray(array $block_array): array
    {
        usort($block_array, function ($a, $b) {
            return $a['sequence'] <=> $b['sequence'];
        });

        return $block_array;

    }
    private function getResponseArray($obj_learn_place, $learn_place_configuration): array {
        $learn_place_location = $obj_learn_place->getLocation();
        $learn_place_blocks = $obj_learn_place->getBlocks();
        $obj_id = $obj_learn_place->getObjectId();
        $result = [
            "id" => $obj_learn_place->getId(),
            "object_id" => $obj_id,
            "title" => \ilObjLearnplaces::_lookupTitle($obj_id),
            "description" => \ilObjLearnplaces::_lookupDescription($obj_id),
            "configuration" => [
                "online" => $learn_place_configuration->isOnline(),
                "default_visibility" => $learn_place_configuration->getDefaultVisibility(),
                "map_zoom_level" => $learn_place_configuration->getMapZoomLevel(),
            ],
            "location" => [
                "latitude" => $learn_place_location->getLatitude(),
                "longitude" => $learn_place_location->getLongitude(),
                "elevation" => $learn_place_location->getElevation(),
                "radius" => $learn_place_location->getRadius(),
            ]
        ];
        $block_array = [];
        foreach ($learn_place_blocks as $block) {
            $block_array[] = $this->getBlockArray($block);

        }
        $result['blocks'] = $this->orderBlockArray($this->filterBlockArray($block_array, $this->removing_block_ids));
        return  $result;
    }

}