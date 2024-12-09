<?php

namespace KPG\Learnplaces\persistence\repository;

use arException;
use ilDatabaseException;
use KPG\Learnplaces\persistence\dto\LearnplaceConstraint;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

/**
 * Class LearnplaceConstraintRepositoryImpl
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class LearnplaceConstraintRepositoryImpl implements LearnplaceConstraintRepository
{
    /**
     * @var LearnplaceRepository $learnplaceRepository
     */
    private $learnplaceRepository;


    /**
     * LearnplaceConstraintRepositoryImpl constructor.
     *
     * @param LearnplaceRepository $learnplaceRepository
     */
    public function __construct(LearnplaceRepository $learnplaceRepository)
    {
        $this->learnplaceRepository = $learnplaceRepository;
    }

    /**
     * @inheritdoc
     */
    public function store(LearnplaceConstraint $constraint): LearnplaceConstraint
    {
        $activeRecord = $this->mapToEntity($constraint);
        $activeRecord->store();
        return $this->mapToDTO($activeRecord);
    }

    /**
     * @inheritdoc
     */
    public function findByBlockId(int $id): LearnplaceConstraint
    {
        $learnplaceConstraintEntity = \KPG\Learnplaces\persistence\entity\LearnplaceConstraint::where(['fk_block_id' => $id])->first();
        return $this->mapToDTO($learnplaceConstraintEntity);
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $constraint = \KPG\Learnplaces\persistence\entity\LearnplaceConstraint::findOrFail($id);
            $constraint->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Learnplace constraint with id \"$id\"", $ex);
        } catch (ilDatabaseException $ex) {
            throw new ilDatabaseException("Could not delete learnplace constraint with id \"$id\".");
        }
    }

    private function mapToDTO(\KPG\Learnplaces\persistence\entity\LearnplaceConstraint $constraint): LearnplaceConstraint
    {

        $learnplaceConstraint = new LearnplaceConstraint();
        $learnplaceConstraint
            ->setPreviousLearnplace($this->learnplaceRepository->find($constraint->getFkPreviousLearnplace()));

        return $learnplaceConstraint;

    }

    private function mapToEntity(LearnplaceConstraint $constraint): \KPG\Learnplaces\persistence\entity\LearnplaceConstraint
    {

        $learnplaceConstraintEntity = new \KPG\Learnplaces\persistence\entity\LearnplaceConstraint($constraint->getId());

        $learnplaceConstraintEntity
            ->setFkPreviousLearnplace($constraint->getPreviousLearnplace()->getId());

        return $learnplaceConstraintEntity;

    }
}
