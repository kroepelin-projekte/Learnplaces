<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Learnplaces
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        global $ilDB;
        $all_learn_places = [];

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
            if (!$obj_learn_place->getConfiguration()->isOnline() OR $obj_learn_place->getConfiguration()->getDefaultVisibility() === "NEVER") {
                continue;
            }
            $all_learn_places[] = [
                "id" => $obj_learn_place->getId(),
                "obj_id" => $obj_learn_place->getObjectId(),
                "title" => \ilObjLearnplaces::_lookupTitle($obj_learn_place->getObjectId()),
                "description" => nl2br(\ilObjLearnplaces::_lookupDescription($obj_learn_place->getObjectId())),
                "tile_image" => $ilias_object_learn_place->getObjectProperties()->getPropertyTileImage()->getTileImage(
                )->getRid()
            ];
        }
        Response::send(200, null, $all_learn_places);
    }
}