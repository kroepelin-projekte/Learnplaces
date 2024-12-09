<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use ilDatabaseException;
use KPG\Learnplaces\persistence\dto\Feedback;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

use function array_map;

/**
 * Class FeedbackRepositoryImpl
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class FeedbackRepositoryImpl implements FeedbackRepository
{
    /**
     * @inheritdoc
     */
    public function store(Feedback $feedback): Feedback
    {
        $activeRecord = $this->mapToEntity($feedback);
        $activeRecord->store();
        return $this->mapToDTO($activeRecord);
    }

    /**
     * @inheritdoc
     */
    public function find(int $id): Feedback
    {
        try {
            $feedbackEntity = \KPG\Learnplaces\persistence\entity\Feedback::findOrFail($id);
            return $this->mapToDTO($feedbackEntity);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Feedback with id \"$id\" not found.", $ex);
        }
    }


    /**
     * @inheritdoc
     */
    public function findByLearnplaceId(int $id): array
    {
        /**
         * @var \KPG\Learnplaces\persistence\entity\Feedback[] $feedbackEntities
         */
        $feedbackEntities = \KPG\Learnplaces\persistence\entity\Feedback::where(['fk_learnplace_id' => $id])->get();

        return array_map(
            function (\KPG\Learnplaces\persistence\entity\Feedback $feedbackEntity) {return $this->mapToDTO($feedbackEntity);},
            $feedbackEntities
        );
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $feedbackEntity = \KPG\Learnplaces\persistence\entity\Feedback::findOrFail($id);
            $feedbackEntity->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Feedback with id \"$id\" not found.", $ex);
        } catch(ilDatabaseException $ex) {
            throw new ilDatabaseException("Unable to delete feedback with id \"$id\"");
        }
    }

    private function mapToDTO(\KPG\Learnplaces\persistence\entity\Feedback $feedbackEntity): Feedback
    {

        $feedback = new Feedback();
        $feedback
            ->setId($feedbackEntity->getPkId())
            ->setContent($feedbackEntity->getContent())
            ->setUserId($feedbackEntity->getFkIluserId());

        return $feedback;
    }

    private function mapToEntity(Feedback $feedback): \KPG\Learnplaces\persistence\entity\Feedback
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\Feedback $activeRecord
         */
        $activeRecord = new \KPG\Learnplaces\persistence\entity\Feedback($feedback->getId());

        $activeRecord
            ->setPkId($feedback->getId())
            ->setContent($feedback->getContent())
            ->setFkIluserId($feedback->getUserId());

        return $activeRecord;
    }

}
