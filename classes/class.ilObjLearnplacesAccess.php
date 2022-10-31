<?php
declare(strict_types=1);

use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\service\publicapi\block\ConfigurationService;

require_once __DIR__ . '/bootstrap.php';

/**
 * Class ilObjLearnplacesAccess
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ilObjLearnplacesAccess extends ilObjectPluginAccess
{

    /**
     * @var ilObjUser $currentUser
     */
    private $currentUser;
    /**
     * @var ilAccessHandler $accessControl
     */
    private $accessControl;

    /**
     * ilObjLearnplacesAccess constructor.
     */
    public function __construct()
    {
        $this->currentUser = PluginContainer::resolve('ilUser');
        $this->accessControl = PluginContainer::resolve('ilAccess');
    }

    /**
     * Checks wether a user may invoke a command or not
     * (this method is called by ilAccessHandler::checkAccess)
     * Please do not check any preconditions handled by
     * ilConditionHandler here. Also don't do usual RBAC checks.
     * @param string $a_cmd        command (not permission!)
     * @param string $a_permission permission
     * @param int    $a_ref_id     reference id
     * @param int    $a_obj_id     object id
     * @param string $a_user_id    user id (if not provided, current user is taken)
     * @return    boolean        true, if everything is ok
     */
    public function _checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id = null)
    {
        if (is_null($a_user_id)) {
            $a_user_id = $this->currentUser->getId();
        }
        switch ($a_permission) {
            case "read":
                if (!self::checkOnline(intval($a_obj_id))
                    && !$this->accessControl->checkAccessOfUser(intval($a_user_id), "write", "", intval($a_ref_id))) {
                    return false;
                }
                break;
        }

        return true;
    }

    public static function fnGroupReadableLearnplacesByCourses(int $a_user_id) : callable
    {
        /**
         * @return int[];
         */
        return function (array $ref_ids) use ($a_user_id) : array {
            global $DIC;
            $tree = $DIC->ilTree;

            $crsRefIds = [];
            foreach ($ref_ids as $ref_id) {
                if ($this->accessControl->checkAccessOfUser($a_user_id, "", "", $ref_id) === true) {
                    $crsRefId = $tree->checkForParentType($ref_id, 'crs', true);
                    if ($crsRefId !== false) {
                        $crsRefIds[$crsRefId] = [...$ref_id];
                    }
                }
            }
            return $crsRefIds;
        };
    }

    /**
     * @param int $objectId
     * @return bool
     */
    public static function checkOnline(int $objectId)
    {
        global $DIC;

        try {
            /**
             * @var ConfigurationService $configurationService
             */
            $configurationService = $DIC[ConfigurationService::class];
            $config = $configurationService->findByObjectId($objectId);
            return $config->isOnline();
        } catch (InvalidArgumentException $ex) {
            return true;
        }

    }
}