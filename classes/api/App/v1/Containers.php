<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use ilObject;

class Containers
{
    public function endpoint(array $params, array $request_body): void
    {

        $all_containers = [];
        foreach ($this->getUserLearnplaceByContainerMembership() as $user_learnplace) {
            $container_ref_id = $user_learnplace['container_ref_id'];
            if(!isset($all_containers[$container_ref_id])) {
                if(\ilObject::_isInTrash($container_ref_id)) {
                    continue;
                }
                if($user_learnplace['container_type'] === 'grp') {
                    $obj_container =  new \ilObjGroup($container_ref_id);
                } else {
                    $obj_container =  new \ilObjCourse($container_ref_id);
                }

                if($obj_container->getOfflineStatus()) {
                    continue;
                }
            }
            $obj_learnplace = PluginContainer::resolve(LearnplaceRepository::class)->findByObjectId($user_learnplace['learnplace_obj_id']);


            if(\ilObject::_isInTrash($user_learnplace['learnplace_ref_id'])) {
                continue;
            }
            if($obj_learnplace->getConfiguration()->isOnline() === false) {
                continue;
            }
            if($obj_learnplace->getConfiguration()->getDefaultVisibility() === "NEVER") {
                continue;
            }
            $learnplace_tags = $this->getTagsByLearnPlaceObjID($user_learnplace['learnplace_obj_id']);
            $container_title = ilObject::_lookupTitle(ilObject::_lookupObjectId($container_ref_id));

            if(isset($all_containers[$container_ref_id])) {
                $all_containers[$container_ref_id]['lernplaces_numbers']++;
                $all_containers[$container_ref_id]['tags'] = array_values(
                    array_unique(array_merge($all_containers[$container_ref_id]['tags'], $learnplace_tags))
                );
            } else {
                $all_containers[$container_ref_id] = [
                    "title" => $container_title,
                    "lernplaces_numbers" => 1,
                    "ref_id" => $container_ref_id,
                    "tags" => $learnplace_tags
                ];
            }
        }
        if ($all_containers == []) {
            Response::send(204);
        }

        $response_array = array_values($all_containers);

        Response::send(200, null, $response_array);
    }

    private function getTagsByLearnPlaceObjID(int $obj_id): array
    {
        $obj_learnplace = PluginContainer::resolve(LearnplaceRepository::class)->findByObjectId($obj_id);
        $learn_place_config = $obj_learnplace->getConfiguration();
        $array_tags = trim($learn_place_config->getTags(), ',');
        $array_tags = explode(',', $array_tags);
        $array_tags = array_map('trim', $array_tags);
        return $array_tags;
    }

    private function getUserLearnplaceByContainerMembership(): array
    {
        global $DIC;
        $assigned_objects = \ilParticipants::_getMembershipByType(
            $DIC->user()->getId(),
            ['crs', 'grp'],
            false,
        );

        $user_learnplaces = [];

        foreach ($assigned_objects as $object_obj_id) {
            foreach (ilObject::_getAllReferences($object_obj_id) as $ref_id) {
                $learnplaces = $DIC->repositoryTree()->getSubTree(
                    $DIC->repositoryTree()->getNodeData($ref_id),
                    true,
                    ['xsrl']
                );
                if (is_array($learnplaces) and !empty($learnplaces)) {
                    foreach ($learnplaces as $learnplace) {
                        $user_learnplaces[] = [
                            'container_ref_id' => $ref_id,
                            'learnplace_obj_id' => $learnplace['obj_id'],
                            'learnplace_ref_id' => $learnplace['ref_id'],
                            'container_type' => ilObject::_lookupType($ref_id, true),
                        ];
                    }
                }
            }
        }

        $result = [];
        $seen = [];

        foreach ($user_learnplaces as $item) {
            $id = $item['learnplace_obj_id'];


            if (!isset($seen[$id]) || ($item['container_type'] === 'grp' && $seen[$id] !== 'grp')) {
                $seen[$id] = $item['container_type'];
                $result[$id] = $item;
            }
        }
        return array_values($result);
    }
}