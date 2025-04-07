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

            # Wieder entfernen
            $DIC->user()->setId(6);

            if (!$DIC->rbac()->system()->checkAccessOfUser(
                $DIC->user()->getId(), 'read', $ilias_object_learn_place->getRefId()
            )) {
                continue;
            }

            if (\ilObject::_isInTrash($ref_id)) {
                continue;
            }

            if (!$obj_learn_place->getConfiguration()->isOnline() ||
                $obj_learn_place->getConfiguration()->getDefaultVisibility() === "NEVER") {
                continue;
            }


            $container_title = $container_information['title'];

            if (isset($all_containers[$container_title])) {
                $all_containers[$container_title]['lernplaces_numbers']++;
            } else {
                $all_containers[$container_title] = [
                    "title" => $container_title,
                    "lernplaces_numbers" => 1,
                    "ref_id" => $container_information['ref_id']
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