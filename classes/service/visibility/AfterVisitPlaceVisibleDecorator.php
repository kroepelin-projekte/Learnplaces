<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\visibility;

use Generator;
use ilObjUser;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use KPG\Learnplaces\service\publicapi\model\BlockModel;
use KPG\Learnplaces\service\publicapi\model\LearnplaceModel;
use KPG\Learnplaces\util\Visibility;

/**
 * Class AfterVisitPlaceVisibleDecorator
 *
 * @package KPG\Learnplaces\service\visibility
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @internal
 */
final class AfterVisitPlaceVisibleDecorator implements LearnplaceService
{
    /**
     * @var ilObjUser $user
     */
    private $user;
    /**
     * @var LearnplaceService $learnplaceService
     */
    private $learnplaceService;

    /**
     * AfterVisitPlaceVisibleDecorator constructor.
     *
     * @param ilObjUser         $user
     * @param LearnplaceService $learnplaceService
     */
    public function __construct(ilObjUser $user, LearnplaceService $learnplaceService)
    {
        $this->user = $user;
        $this->learnplaceService = $learnplaceService;
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        $this->learnplaceService->delete($id);
    }

    /**
     * @inheritDoc
     */
    public function findByObjectId(int $objectId): LearnplaceModel
    {
        $learnplace = $this->learnplaceService->findByObjectId($objectId);
        if (!$this->hasVisitedPlace($learnplace)) {
            $blocks = $this->filterInvisibleBlocks($learnplace->getBlocks());
            $learnplace->setBlocks(iterator_to_array($blocks));
        }
        return $learnplace;
    }

    /**
     * Filters all blocks with the AFTER_VISIT_PLACE visibility.
     *
     * @param BlockModel[] $blocks The blocks which should be filtered.
     *
     * @return Generator The filtered block list.
     *
     * @see Visibility::AFTER_VISIT_PLACE
     */
    private function filterInvisibleBlocks(array $blocks): Generator
    {
        foreach ($blocks as $block) {

            if($block instanceof AccordionBlockModel) {
                $accordionBlocks = $this->filterInvisibleBlocks($block->getBlocks());
                $block->setBlocks(iterator_to_array($accordionBlocks));
            }

            if(strcmp($block->getVisibility(), Visibility::AFTER_VISIT_PLACE) !== 0) {
                yield $block;
            }
        }
        return;
    }

    /**
     * @param LearnplaceModel $learnplace
     * @return bool
     */
    private function hasVisitedPlace(LearnplaceModel $learnplace): bool
    {
        foreach ($learnplace->getVisitJournals() as $visit) {

            //the user id could also be string
            if($visit->getUserId() === intval($this->user->getId())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function store(LearnplaceModel $learnplaceModel): LearnplaceModel
    {
        // TODO: Implement store() method.
        return $learnplaceModel;
    }

    /**
     * @inheritDoc
     */
    public function find(int $id): LearnplaceModel
    {
        return $this->findByObjectId($id);
    }
}
