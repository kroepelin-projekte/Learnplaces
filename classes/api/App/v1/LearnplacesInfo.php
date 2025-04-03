<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use KPG\Learnplaces\persistence\dto\Block;
use KPG\Learnplaces\persistence\dto\Configuration;
use KPG\Learnplaces\persistence\dto\Learnplace;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use ilObject;
use ILIAS\Data\ReferenceId;
use KPG\Learnplaces\persistence\entity\VisitJournal;

class LearnplacesInfo
{
    private array $removing_block_ids = [];

    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        $id = htmlspecialchars($params['id']);

        try {
            $obj_learn_place = PluginContainer::resolve(LearnplaceRepository::class)->find($id);
        } catch (\Exception $e) {
            Response::send(400, "LEARNPLACE_NOT_FOUND", []);
        }
        foreach (\ilObjLearnplaces::_getAllReferences((int) $obj_learn_place->getObjectId()) as $ref_id) {
            if (\ilObject::_isInTrash($ref_id)) {
                Response::send(400, "LEARNPLACE_NOT_FOUND", []);
            }
            break;
        };

        if (!$obj_learn_place->getConfiguration()->isOnline() or $obj_learn_place->getConfiguration(
            )->getDefaultVisibility() === "NEVER") {
            Response::send(400, "LEARNPLACE_NOT_FOUND", []);
        }
        Response::send(200, null, $this->getResponseArray($obj_learn_place, $obj_learn_place->getConfiguration()));
    }

    private function getBlockArray(Block $block, int $learn_place_id): array|false
    {
        global $DIC;

        if ($block->getVisibility() === "NEVER") {
            return false;
        }
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
            $block_array['description'] = nl2br($block->getDescription());
        }

        if (method_exists($block, 'getPicture')) {
            $block_array['picture'] = $block->getPicture()->getResourceId();
        }

        if (method_exists($block, 'getRefId')) {
            $block_array['ilias_ref_id'] = $block->getRefId();

            $url = $DIC['static_url']->builder()->build(
                ilObject::_lookupType(ilObject::_lookupObjectId($block->getRefId())),
                new ReferenceId($block->getRefId()),
            )->__toString();

            $block_array['ilias_obj_url'] = preg_replace('#/api/learnplaceapp/v1/learnplaces/\d+#', '', $url);
            $block_array["ilias_obj_title"] = ilObject::_lookupTitle(ilObject::_lookupObjectId($block->getRefId()));
        }

        if (method_exists($block, 'getResourceId')) {
            $block_array['resource_id'] = $block->getResourceId();
        }

        if (method_exists($block, 'getBlocks')) {
            $sub_blocks = $block->getBlocks();
            $sub_block_array = [];

            foreach ($sub_blocks as $sub_block) {
                $this->removing_block_ids[] = $sub_block->getId();
                $array = $this->getBlockArray($sub_block, $learn_place_id);
                if ($array !== false) {
                    $sub_block_array[] = $array;
                }
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

    private function getResponseArray(Learnplace $obj_learn_place, Configuration $learn_place_configuration): array
    {
        global $DIC;
        $learn_place_location = $obj_learn_place->getLocation();
        $learn_place_blocks = $obj_learn_place->getBlocks();
        $obj_id = $obj_learn_place->getObjectId();
        global $ilDB;
        $result = $ilDB->query(
            "SELECT * FROM ilias.xsrl_visit_journal WHERE fk_learnplace_id = " . $obj_learn_place->getID(
            ) . " AND user_id = " . $DIC->user()->getId()
        );

        $result = [
            "id" => $obj_learn_place->getId(),
            "object_id" => $obj_id,
            "title" => \ilObjLearnplaces::_lookupTitle($obj_id),
            "description" => nl2br(\ilObjLearnplaces::_lookupDescription($obj_id)),
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
            ],
            "visited" => $result->rowCount() > 0,
        ];
        $block_array = [];
        foreach ($learn_place_blocks as $block) {
            $array = $this->getBlockArray($block, (int) $obj_learn_place->getId());
            if ($array !== false) {
                $block_array[] = $array;
            }
        }
        $result['blocks'] = $this->orderBlockArray($this->filterBlockArray($block_array, $this->removing_block_ids));
        return $result;
    }
}