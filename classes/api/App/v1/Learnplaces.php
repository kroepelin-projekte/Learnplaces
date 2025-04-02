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
        global $ilDB;
        global $DIC;
        $all_learn_places = [];
        $all_container_title = [];

        $result = $ilDB->query("SELECT pk_id FROM xsrl_learnplace");

        if ($result->rowCount() === 0) {
            Response::send(204);
        }

        while ($record = $ilDB->fetchAssoc($result)) {
            $obj_learn_place = PluginContainer::resolve(LearnplaceRepository::class)->find((int) $record['pk_id']);

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
            $user_id = $DIC->user()->getId();
            if (!$DIC->rbac()->system()->checkAccessOfUser($user_id, 'read', $ref_id)) {
                continue;
            }
            if(!$container_title = $this->getContainer($ilias_object_learn_place->getRefId())) {
                continue;
            }
            $all_container_title[] = $container_title;
            global $ilDB;
            $visit_result = $ilDB->query(
                "SELECT * FROM ilias.xsrl_visit_journal WHERE fk_learnplace_id = " . $obj_learn_place->getID(
                ) . " AND user_id = " . $DIC->user()->getId()
            );

            $all_learn_places[] = [
                "id" => $obj_learn_place->getId(),
                "obj_id" => $obj_learn_place->getObjectId(),
                "title" => \ilObjLearnplaces::_lookupTitle($obj_learn_place->getObjectId()),
                "description" => nl2br(\ilObjLearnplaces::_lookupDescription($obj_learn_place->getObjectId())),
                "tile_image" => $ilias_object_learn_place->getObjectProperties()->getPropertyTileImage()->getTileImage(
                )->getRid(),
                "visited" => $visit_result->rowCount() > 0,
                "container_title" => $container_title,
            ];
        }
        //$all_learn_places['containers'] = array_unique($all_container_title);
        Response::send(200, null, $all_learn_places);
    }

    public function getContainer($ref_id): string|bool
    {
        global $DIC;
        $parent = $DIC->repositoryTree()->getParentId($ref_id);
        if ($parent == 1) {
            return false;
        }
        if (ilObject::_isInTrash($parent)) {
            return false;
        }
        $obj_id = ilObject::_lookupObjectId($parent);
        if (ilObject::_lookupType($obj_id) == "crs" || ilObject::_lookupType($obj_id) == "grp") {
            return ilObject::_lookupTitle($obj_id);
        }
        return $this->getContainer($parent);
    }
}