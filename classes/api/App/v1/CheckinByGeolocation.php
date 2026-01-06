<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use ilObject;
use KPG\Learnplaces\persistence\entity\VisitJournal;
use ILIAS\DI\Container;

class CheckinByGeolocation
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        global $DIC;
        $db = $DIC->database();

        if (!isset($params['id'])) {
            Response::send(400, null, ["error" => "checkin error"]);
        }

        $learnplace_id = $params['id'];
        $learnplace = PluginContainer::resolve(LearnplaceRepository::class)->find($learnplace_id);
        $title = ilObject::_lookupTitle($learnplace->getObjectId());

        $result = $db->queryF(
            "SELECT * FROM xsrl_visit_journal WHERE fk_learnplace_id = %s AND user_id = %s",
            ['integer', 'integer'],
            [$learnplace_id, $DIC->user()->getId()]
        );

        // Already checked in
        if ($result->rowCount() > 0) {
            Response::send(200, null, ["status" => "QR_CODE_USER_WAS_HERE", "id" => $learnplace_id, "title" => $title]);
        }

        // No read access to learnplace
        if(!$this->checkAccessible((int) $learnplace_id, $DIC)){
            Response::send(200, DEVMODE ? "QR_CODE_ACCESS_DENIED" : null, ["status" => "QR_CODE_ACCESS_DENIED"]);
        }

        // Update learning progress
        $obj_learnplace = \ilObjectFactory::getInstanceByObjId($learnplace->getObjectId());
        $obj_learnplace->setLearningProgressStatus(\ilLPStatus::LP_STATUS_COMPLETED_NUM);

        $ar_visit = new VisitJournal();
        $ar_visit->setUserId($DIC->user()->getId())->setFkLearnplaceId($learnplace_id)->setTime(time())->create();
        Response::send(200, null, ["status" => "QR_CODE_USER_FIRST_TIME_HERE", "id" => $learnplace_id, "title" => $title]);
    }

    /**
     * Checks if the user has read access to a learning place.
     *
     * @param int       $learn_place_id The ID of the learning place to check.
     * @param Container $DIC            The dependency injection container providing system services.
     *
     * @return bool Returns true if the user has read access, otherwise false.
     */
    public function checkAccessible(int $learn_place_id, Container $DIC): bool {

        $obj_learn_place = PluginContainer::resolve(LearnplaceRepository::class)->find($learn_place_id);
        foreach (\ilObjLearnplaces::_getAllReferences((int) $obj_learn_place->getObjectId()) as $learn_place_ref_id) {
            if($DIC->rbac()->system()->checkAccess('read', $learn_place_ref_id)) {
                return true;
            }

        }
        return false;
    }
}