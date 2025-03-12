<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use ilObjLearnplaces;

class Learnplaces
{
    public function endpoint(array $params, array $request_body)
    {
        global $ilDB;
        $all_learn_places = [];

        $result = $ilDB->query("SELECT pk_id FROM xsrl_learnplace");
        if ($result === null) {
            Response::send("200");
        }
        while ($record = $ilDB->fetchAssoc($result)) {
            $obj_learn_place = PluginContainer::resolve(LearnplaceRepository::class)->find((int)$record['pk_id']);

            foreach(\ilObjLearnplaces::_getAllReferences((int)$obj_learn_place->getObjectId()) as $ref_id) {
                $ilias_object_learn_place = new \ilObjLearnplaces($ref_id);
                break;
            };

            $all_learn_places[] = [
                "id" => $obj_learn_place->getId(),
                "obj_id" => $obj_learn_place->getObjectId(),
                "title" => \ilObjLearnplaces::_lookupTitle($obj_learn_place->getObjectId()),
                "description" => \ilObjLearnplaces::_lookupDescription($obj_learn_place->getObjectId()),
                "tile_image" => $ilias_object_learn_place->getObjectProperties()->getPropertyTileImage()->getTileImage()->getRid()
            ];
        }
        Response::send("200", null, $all_learn_places);
    }
}