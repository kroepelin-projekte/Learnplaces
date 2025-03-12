<?php
namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;

class LearnplacesInfo
{
    public function endpoint(array $params, array $request_body) {
        global $ilDB;
        $id = $params['id'];
        $sql = "
            SELECT xsrl_learnplace.pk_id                    as learnplacesID,
                   xsrl_configuration.map_zoom_level        as mapZoom,
                   xsrl_configuration.fk_visibility_default as visibilityDefault,
                   xsrl_location.latitude                   as latitude,
                   xsrl_location.longitude                  as longitude,
                   xsrl_location.radius                     as radius
            FROM xsrl_learnplace
                     INNER JOIN xsrl_configuration ON xsrl_learnplace.fk_configuration = xsrl_configuration.pk_id
                     INNER JOIN xsrl_location ON xsrl_learnplace.pk_id   = xsrl_location.fk_learnplace_id
            WHERE xsrl_learnplace.pk_id = 
      " . $ilDB->quote($id);
        $result = $ilDB->query($sql);
        $result = $ilDB->fetchAssoc($result);
        if($result === null) {
            Response::send(400, 'LEARNPLACE_NOT_FOUND', []);
        }
        $result['blocks'] = [];
        $irgendwas = PluginContainer::resolve(LearnplaceRepository::class);
        $blocks = $irgendwas->find(1);
        Response::send(200, NULL, $result);
    }
}