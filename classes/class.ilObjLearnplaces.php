<?php

declare(strict_types=1);

use ILIAS\Filesystem\Exception\FileNotFoundException;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\service\publicapi\block\ConfigurationService;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\block\LocationService;
use KPG\Learnplaces\service\publicapi\model\ConfigurationModel;
use KPG\Learnplaces\service\publicapi\model\LearnplaceModel;
use KPG\Learnplaces\service\publicapi\model\LocationModel;
use KPG\Learnplaces\service\publicapi\block\util\BlockOperationDispatcher;
use KPG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use KPG\Learnplaces\service\filesystem\PathHelper;
use KPG\Learnplaces\service\publicapi\model\PictureModel;
use KPG\Learnplaces\persistence\repository\PictureRepository;
use KPG\Learnplaces\service\publicapi\model\VideoBlockModel;
use KPG\Learnplaces\service\publicapi\model\PictureBlockModel;

/**
 * Class ilObjLearnplaces
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ilObjLearnplaces extends ilObjectPlugin implements ilLPStatusPluginInterface
{
    /*
    const TRANSLATE_NEWS_TITLE = true;
    const TRANSLATE_NEWS_CONTENT = 0; //has to be a number
    */

    protected function initType(): void
    {
        $this->setType(ilLearnplacesPlugin::PLUGIN_ID);
    }

    protected function doCreate(bool $clone_mode = false): void
    {
        /**
         * @var LearnplaceService $learnplaceService
         */
        $learnplaceService = PluginContainer::resolve(LearnplaceService::class);
        /**
         * @var ConfigurationService $configService
         */
        $configService = PluginContainer::resolve(ConfigurationService::class);
        /**
         * @var LocationService $locationService
         */
        $locationService = PluginContainer::resolve(LocationService::class);

        $location = $locationService->store(new LocationModel());
        $config = $configService->store((new ConfigurationModel())->setQrCodeToken(bin2hex(random_bytes(32))));
        $learnplace = new LearnplaceModel();
        $learnplace
            ->setLocation($location)
            ->setConfiguration($config)
            ->setObjectId(intval($this->getId()));

        $learnplaceService->store($learnplace);
        /*
        $news = new ilNewsItem();
        $news->setUserId($this->getOwner());
        $news->setTitle('news_created');
        $news->setContentIsLangVar(self::TRANSLATE_NEWS_TITLE);
        $news->setContentTextIsLangVar(self::TRANSLATE_NEWS_CONTENT);
        $news->setContent('');
        $news->setContextObjId($this->getId());
        $news->setContextObjType($this->getType());
        $news->setCreationDate($this->getCreateDate());
        $news->create();
        */
    }

    protected function doRead(): void
    {

    }

    protected function doUpdate(): void
    {
        /*
        $user = PluginContainer::resolve('ilUser');

        $news = new ilNewsItem(ilNewsItem::getLastNewsIdForContext($this->getId(), $this->getType()));
        $news->setTitle('news_updated');
        $news->setContentIsLangVar(self::TRANSLATE_NEWS_TITLE);
        $news->setContentTextIsLangVar(self::TRANSLATE_NEWS_CONTENT);
        $news->setContent('');
        $news->setUpdateDate($this->getLastUpdateDate());
        $news->setUpdateUserId($user->getId());
        $news->update();
        */
    }

    protected function doDelete(): void
    {
        /**
         * @var LearnplaceService $learnplaceService
         */
        $learnplaceService = PluginContainer::resolve(LearnplaceService::class);
        $learnplace = $learnplaceService->findByObjectId(intval($this->getId()));
        $learnplaceService->delete($learnplace->getId());
    }

    /**
     * @param ilObject2    $new_obj - cloned object
     * @param int          $a_target_id
     * @param int|null     $a_copy_id
     */
    protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = null): void
    {
        /**
         * @var LearnplaceService $learnplaceService
         */
        $learnplaceService = PluginContainer::resolve(LearnplaceService::class);

        /**
         * @var ConfigurationService $configurationService
         */
        $configurationService = PluginContainer::resolve(ConfigurationService::class);

        /**
         * @var LocationService $locationService
         */
        $locationService = PluginContainer::resolve(LocationService::class);

        /**
         * @var BlockOperationDispatcher $blockDispatcher
         */
        $blockDispatcher = PluginContainer::resolve(BlockOperationDispatcher::class);

        $newObjectId = intval($new_obj->getId());
        $learnplace = $learnplaceService->findByObjectId(intval($this->getId()));

        // ILIAS calls do create first so we have an empty learnplace with the new object id
        $copyLearnplace = $learnplaceService->findByObjectId($newObjectId);

        //Copy Location
        $locationService->store(
            $learnplace->getLocation()->setId($copyLearnplace->getLocation()->getId())
        );

        // Copy Object configuration
        $configurationService->store(
            $learnplace->getConfiguration()->setId($copyLearnplace->getConfiguration()->getId())
        );

        // Copy pictures
        $pictures = $learnplace->getPictures();
        $copyPictures = [];
        foreach ($pictures as $picture) {
            $copyPictures[] = $this->copyPictureModel($newObjectId, $picture);
        }
        $copyLearnplace->setPictures($copyPictures);

        // Copy Blocks
        $blocks = $learnplace->getBlocks();
        foreach ($blocks as $block) {
            $block->setId(0);

            // Copy blocks which belong to an accordion
            if ($block instanceof AccordionBlockModel) {
                $accBlocks = $block->getBlocks();
                foreach ($accBlocks as $accBlock) {
                    $accBlock->setId(0);

                    if ($accBlock instanceof VideoBlockModel) {
                        $resourceId = $accBlock->getResourceId();

                        $newResourceId = $this->copyFileToNewObject($newObjectId, $resourceId);

                        $accBlock->setResourceId($newResourceId);
                    }

                    if ($accBlock instanceof PictureBlockModel) {
                        $accBlock->setPicture($this->copyPictureModel($newObjectId, $accBlock->getPicture()));
                    }
                }

                $copyAccBlocks = $blockDispatcher->storeAll($accBlocks);
                $block->setBlocks($copyAccBlocks);
            }

            if ($block instanceof VideoBlockModel) {
                $resourceId = $block->getResourceId();

                $newResourceId = $this->copyFileToNewObject($newObjectId, $resourceId);

                $block->setResourceId($newResourceId);
            }

            if ($block instanceof PictureBlockModel) {
                $block->setPicture($this->copyPictureModel($newObjectId, $block->getPicture()));
            }
        }
        $copyBlocks = $blockDispatcher->storeAll($blocks);
        $copyLearnplace->setBlocks($copyBlocks);

        $learnplaceService->store($copyLearnplace);
    }

    /**
     * @param int $objectId
     * @param string $resourceId
     * @return string
     * @throws ilException
     */
    private function copyFileToNewObject(int $objectId, string $resourceId): string
    {
        if (strlen($resourceId) === 0) {
            return '';
        }

        /** @var \ILIAS\ResourceStorage\Services $resourceStorage  */
        $resourceStorage = PluginContainer::resolve('resourceStorage');

        if (! $resourceStorage->manage()->find($resourceId)) {
            return '';
        }

        $stream = $resourceStorage->consume()->stream(new ResourceIdentification($resourceId))
            ->getStream();

        $newResourceId = $resourceStorage->manage()->stream($stream, new ilLearnplacesStakeholder())
            ->serialize();

        return $newResourceId;
    }

    /**
     * @param int $newObjectId
     * @param PictureModel $picture
     * @return PictureModel
     * @throws ilException
     */
    private function copyPictureModel(int $newObjectId, PictureModel $picture): PictureModel
    {
        /**
         * @var PictureRepository $pictureRepository
         */
        $pictureRepository = PluginContainer::resolve(PictureRepository::class);

        $copyPicture = new PictureModel();
        $newResourceId = $this->copyFileToNewObject($newObjectId, $picture->getResourceId());

        $copyPicture->setResourceId($newResourceId);
        return ($pictureRepository->store($copyPicture->toDto()))->toModel();
    }

    /**
     * @return array|int[]
     */
    public function getLPCompleted(): array
    {
        $user = [];
        foreach (\ilLPMarks::_getAllUserIds($this->getId()) as $user_id) {
            if (\ilLPStatus::_lookupStatus($this->getId(), $user_id) === ilLPStatus::LP_STATUS_COMPLETED_NUM) {
                $user[] = $user_id;
            }
        }
        return $user;
    }

    /**
     * @return array|int[]
     */
    public function getLPNotAttempted(): array
    {
        $user = [];
        foreach (\ilLPMarks::_getAllUserIds($this->getId()) as $user_id) {
            if (\ilLPStatus::_lookupStatus($this->getId(), $user_id) === ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM) {
                $user[] = $user_id;
            }
        }
        return $user;
    }

    /**
     * @return array
     */
    public function getLPFailed(): array
    {
        $user = [];
        foreach (\ilLPMarks::_getAllUserIds($this->getId()) as $user_id) {
            if (\ilLPStatus::_lookupStatus($this->getId(), $user_id) === ilLPStatus::LP_STATUS_FAILED_NUM) {
                $user[] = $user_id;
            }
        }
        return $user;
    }

    /**
     * @return array
     */
    public function getLPInProgress(): array
    {
        $user = [];
        foreach (\ilLPMarks::_getAllUserIds($this->getId()) as $user_id) {
            if (\ilLPStatus::_lookupStatus($this->getId(), $user_id) === ilLPStatus::LP_STATUS_IN_PROGRESS_NUM) {
                $user[] = $user_id;
            }
        }
        return $user;
    }

    /**
     * @param int $a_user_id
     * @return int
     */
    public function getLPStatusForUser(int $a_user_id): int
    {
        global $DIC;
        $a_obj_id = $this->getId();

        $ilDB = $DIC['ilDB'];

        $set = $ilDB->query(
            "SELECT status" .
            " FROM ut_lp_marks" .
            " WHERE obj_id = " . $ilDB->quote($a_obj_id, "integer") .
            " AND usr_id = " . $ilDB->quote($a_user_id, "integer")
        );
        $row = $ilDB->fetchAssoc($set);
        $status = $row["status"];
        if (!$status) {
            $status = ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM;
        }
        return $status;
    }

    /**
     * @param int      $il_lpstatus_num
     * @param int|null $user_id
     * @return void
     */
    public function setLearningProgressStatus(int $il_lpstatus_num, int $user_id = null): void
    {
        if ($user_id === null) {
            global $ilUser;
            $user_id = $ilUser->getId();
        }

        $_SESSION[\ilObjLearnplacesGUI::LP_SESSION_ID] = $il_lpstatus_num;
        ilLPStatusWrapper::_updateStatus($this->getId(), $user_id);
    }
}
