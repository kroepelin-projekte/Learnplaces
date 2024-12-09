<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\block\util;

use LogicException;
use KPG\Learnplaces\service\publicapi\block\AccordionBlockService;
use KPG\Learnplaces\service\publicapi\block\ILIASLinkBlockService;
use KPG\Learnplaces\service\publicapi\block\MapBlockService;
use KPG\Learnplaces\service\publicapi\block\PictureBlockService;
use KPG\Learnplaces\service\publicapi\block\PictureUploadBlockService;
use KPG\Learnplaces\service\publicapi\block\RichTextBlockService;
use KPG\Learnplaces\service\publicapi\block\VideoBlockService;
use KPG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use KPG\Learnplaces\service\publicapi\model\BlockModel;
use KPG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use KPG\Learnplaces\service\publicapi\model\MapBlockModel;
use KPG\Learnplaces\service\publicapi\model\PictureBlockModel;
use KPG\Learnplaces\service\publicapi\model\PictureUploadBlockModel;
use KPG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use KPG\Learnplaces\service\publicapi\model\VideoBlockModel;

/**
 * Class DefaultBlockOperationDispatcher
 *
 * @package KPG\Learnplaces\service\publicapi\block\util
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class DefaultBlockOperationDispatcher implements BlockOperationDispatcher
{
    /**
     * @var AccordionBlockService $accordionBlockService
     */
    private $accordionBlockService;
    /**
     * @var ILIASLinkBlockService $iliasLinkBlockService
     */
    private $iliasLinkBlockService;
    /**
     * @var PictureBlockService $pictureBlockService
     */
    private $pictureBlockService;
    /**
     * @var PictureUploadBlockService $pictureUploadBlockService
     */
    private $pictureUploadBlockService;
    /**
     * @var MapBlockService $mapBlockService
     */
    private $mapBlockService;
    /**
     * @var RichTextBlockService $richTextBlockService
     */
    private $richTextBlockService;
    /**
     * @var VideoBlockService $videoBlockService
     */
    private $videoBlockService;


    /**
     * DefaultBlockOperationDispatcher constructor.
     *
     * @param AccordionBlockService     $accordionBlockService
     * @param ILIASLinkBlockService     $iliasLinkBlockService
     * @param PictureBlockService       $pictureBlockService
     * @param PictureUploadBlockService $pictureUploadBlockService
     * @param MapBlockService           $mapBlockService
     * @param RichTextBlockService      $richTextBlockService
     * @param VideoBlockService         $videoBlockService
     */
    public function __construct(AccordionBlockService $accordionBlockService, ILIASLinkBlockService $iliasLinkBlockService, PictureBlockService $pictureBlockService, PictureUploadBlockService $pictureUploadBlockService, MapBlockService $mapBlockService, RichTextBlockService $richTextBlockService, VideoBlockService $videoBlockService)
    {
        $this->accordionBlockService = $accordionBlockService;
        $this->iliasLinkBlockService = $iliasLinkBlockService;
        $this->pictureBlockService = $pictureBlockService;
        $this->pictureUploadBlockService = $pictureUploadBlockService;
        $this->mapBlockService = $mapBlockService;
        $this->richTextBlockService = $richTextBlockService;
        $this->videoBlockService = $videoBlockService;
    }


    /**
     * @inheritDoc
     */
    public function deleteAll(array $blocks)
    {
        foreach ($blocks as $block) {
            $this->deleteBlockByType($block);
        }
    }

    /**
     * @inheritDoc
     */
    public function storeAll(array $blocks): array
    {
        $storedBlockModels = [];
        foreach ($blocks as $block) {
            $storedBlockModels[] = $this->storeBlockByType($block);
        }

        return $storedBlockModels;
    }



    private function deleteBlockByType(BlockModel $block)
    {
        switch (true) {
            case $block instanceof AccordionBlockModel:
                $this->accordionBlockService->delete($block->getId());
                return;
            case $block instanceof PictureBlockModel:
                $this->pictureBlockService->delete($block->getId());
                return;
            case $block instanceof ILIASLinkBlockModel:
                $this->iliasLinkBlockService->delete($block->getId());
                return;
            case $block instanceof PictureUploadBlockModel:
                $this->pictureUploadBlockService->delete($block->getId());
                return;
            case $block instanceof MapBlockModel:
                $this->mapBlockService->delete($block->getId());
                return;
            case $block instanceof RichTextBlockModel:
                $this->richTextBlockService->delete($block->getId());
                return;
            case $block instanceof VideoBlockModel:
                $this->videoBlockService->delete($block->getId());
                return;
            default:
                throw new LogicException('Unable to dispatch block delete operation');
        }
    }

    private function storeBlockByType(BlockModel $block): BlockModel
    {
        switch (true) {
            case $block instanceof AccordionBlockModel:
                return $this->accordionBlockService->store($block);
            case $block instanceof PictureBlockModel:
                return $this->pictureBlockService->store($block);
            case $block instanceof ILIASLinkBlockModel:
                return $this->iliasLinkBlockService->store($block);
            case $block instanceof PictureUploadBlockModel:
                return $this->pictureUploadBlockService->store($block);
            case $block instanceof MapBlockModel:
                return $this->mapBlockService->store($block);
            case $block instanceof RichTextBlockModel:
                return $this->richTextBlockService->store($block);
            case $block instanceof VideoBlockModel:
                return $this->videoBlockService->store($block);
            default:
                throw new LogicException('Unable to dispatch block store operation');
        }
    }
}
