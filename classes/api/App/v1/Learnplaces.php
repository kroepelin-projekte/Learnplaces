<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;

class Learnplaces
{
    public function endpoint(array $params, array $request_body)
    {
        global $ilDB;
        $all_learnplaces = [];
        $sql = "SELECT * FROM xsrl_learnplace";

        $result = $ilDB->query($sql);
        if ($result === null) {
            Response::send("200");
        }
        while ($record = $ilDB->fetchAssoc($result)) {
            foreach (\ilObjLearnplaces::_getAllReferences($record['fk_object_id']) as $ref_id) {
                $obj = new \ilObjLearnplaces($ref_id);
                break;
            }
            $all_learnplaces[] = [
                'id' => $record['pk_id'],
                'title' => $obj->getTitle(),
                'description' => $obj->getDescription(),
                "tile" => $obj->getObjectProperties()->getPropertyTileImage()->getTileImage()->getRID()
            ];
        }
        Response::send("200", null, $all_learnplaces);
    }
}