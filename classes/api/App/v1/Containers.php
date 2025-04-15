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
        global $DIC;

        $all_containers = [];

        foreach (PluginContainer::resolve(LearnplaceRepository::class)->get() as $obj_learn_place) {

            foreach (\ilObjLearnplaces::_getAllReferences((int) $obj_learn_place->getObjectId()) as $ref_id) {
                $ilias_object_learn_place = new \ilObjLearnplaces($ref_id);
                break;
            }
            if (!$container_information = $this->getContainer($ilias_object_learn_place->getRefId())) {
                continue;
            }

            if (!$DIC->rbac()->system()->checkAccessOfUser(
                $DIC->user()->getId(), 'read', $ilias_object_learn_place->getRefId()
            )) {
                continue;
            }

            if (\ilObject::_isInTrash($ref_id)) {
                continue;
            }
            $learn_place_config = $obj_learn_place->getConfiguration();
            if (!$learn_place_config->isOnline() ||
                $learn_place_config->getDefaultVisibility() === "NEVER") {
                continue;
            }

            $array_tags = explode(',', $learn_place_config->getTags());
            $array_tags = array_map('trim', $array_tags);

            $container_title = $container_information['title'];
            $container_ref_id = $container_information['ref_id'];

            if (isset($all_containers[$container_ref_id])) {
                $all_containers[$container_ref_id]['lernplaces_numbers']++;
                $all_containers[$container_ref_id]['tags'] = array_unique(array_merge($all_containers[$container_ref_id]['tags'], $array_tags));
            } else {
                $all_containers[$container_ref_id] = [
                    "title" => $container_title,
                    "lernplaces_numbers" => 1,
                    "ref_id" => $container_ref_id,
                    "tags" => $array_tags
                ];
            }
        }
        if($all_containers == []) {
            Response::send(204);
        }

        $response_array = array_values($all_containers);

        Response::send(200, null, $response_array);
    }

    public function getContainer(int|bool $ref_id): array|bool
    {
        global $DIC;
        $result = $DIC->repositoryTree()->getPathFull($ref_id);
        for ($i = count($result) - 2; $i >= 0; $i--) {
            if ($result[$i]['type'] == "crs" || $result[$i]['type'] == "grp") {
                if ($result[$i]['ref_id'] == 1 || ilObject::_isInTrash($result[$i]['ref_id'])) {
                    return false;
                }
                return [
                    "ref_id" => $result[$i]['ref_id'],
                    "title" => $result[$i]['title']
                ];
                break;
            }
        }
        return false;
    }
}