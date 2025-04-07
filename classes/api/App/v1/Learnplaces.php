<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use KPG\Learnplaces\persistence\entity\VisitJournal;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use ilObject;

class Learnplaces
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        if(!is_numeric($params['container_ref_id'])) {
            Response::send(400, null, ['error' => 'INVALID_CONTAINER_REF_ID']);
        }
        $container_ref_id = $params['container_ref_id'];
        $learn_places_ref_id = $this->getContainerLearnPlacesObjectID($container_ref_id);
        if (empty($learn_places_ref_id)) {
            Response::send(200, null, []);
            return;
        }

        global $ilDB;
        global $DIC;
        $all_learn_places = [];
        $all_learn_places['container_title'] =  ilObject::_lookupTitle(ilObject::_lookupObjectId($container_ref_id));
        $all_learn_places['learn_places'] = [];
        $quoted_ids = array_map(fn($id) => $ilDB->quote($id, "integer"), $learn_places_ref_id);

        foreach (PluginContainer::resolve(LearnplaceRepository::class)->getWhere($quoted_ids) as $obj_learn_place) {
            foreach (\ilObjLearnplaces::_getAllReferences((int) $obj_learn_place->getObjectId()) as $ref_id) {
                $ilias_object_learn_place = new \ilObjLearnplaces($ref_id);
                break;
            };

            if (\ilObject::_isInTrash($ref_id)) {
                continue;
            }
            if (!$obj_learn_place->getConfiguration()->isOnline() or $obj_learn_place->getConfiguration(
                )->getDefaultVisibility() === "NEVER") {
                continue;
            }
            //Wieder entfernen
            $DIC->user()->setId(6);
            $user_id = $DIC->user()->getId();
            if (!$DIC->rbac()->system()->checkAccessOfUser($user_id, 'read', $ref_id)) {
                continue;
            }
            if(!$container = $this->getContainer($ilias_object_learn_place->getRefId())) {
                continue;
            }
            if($container['ref_id'] != $container_ref_id) {
                continue;
            }

            global $ilDB;
            $visit_result = $ilDB->query(
                "SELECT * FROM ilias.xsrl_visit_journal WHERE fk_learnplace_id = " . $obj_learn_place->getID(
                ) . " AND user_id = " . $DIC->user()->getId()
            );

            $all_learn_places['learn_places'][] = [
                "id" => $obj_learn_place->getId(),
                "obj_id" => $obj_learn_place->getObjectId(),
                "title" => \ilObjLearnplaces::_lookupTitle($obj_learn_place->getObjectId()),
                "description" => nl2br(\ilObjLearnplaces::_lookupDescription($obj_learn_place->getObjectId())),
                "tile_image" => $ilias_object_learn_place->getObjectProperties()->getPropertyTileImage()->getTileImage(
                )->getRid(),
                "visited" => $visit_result->rowCount() > 0,
            ];

        }
        Response::send(200, null, $all_learn_places);
    }

    public function getContainer(int|bool $ref_id): array|bool
    {
        global $DIC;

        $parent = $DIC->repositoryTree()->getParentId($ref_id);

        if ($parent == 1 || ilObject::_isInTrash($parent)) {
            return false;
        }

        $obj_id = ilObject::_lookupObjectId($parent);

        if (ilObject::_lookupType($obj_id) == "crs" || ilObject::_lookupType($obj_id) == "grp") {
            foreach (\ilObjLearnplaces::_getAllReferences($obj_id) as $ref_id) {
                $container_ref_id = $ref_id;
                break;
            };

            return [
                "ref_id" => $container_ref_id,
                "title" => ilObject::_lookupTitle($obj_id),
            ];
        }
        return $this->getContainer($parent);
    }

    public function getContainerLearnPlacesObjectID(int $container_ref_id) : array
    {
        global $DIC;
        $container_childs_ref_id = $DIC->repositoryTree()->getSubTreeIds((int) $container_ref_id);
        foreach ($container_childs_ref_id as $ref_id) {
            $obj_id = ilObject::_lookupObjectId($ref_id);
            if (ilObject::_lookupType($obj_id) == "xsrl" ) {
                $result[] = $obj_id;
            }
        }
        return $result;
    }
}