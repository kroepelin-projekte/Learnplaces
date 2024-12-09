<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\LearnplaceConstraint;
use KPG\Learnplaces\service\publicapi\model\LearnplaceConstraintModel;

/**
 * Trait LearnplaceConstraintDtoMappingAware
 *
 * Adds the functionality to map learnplace constraint model to leanplace constraint dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait LearnplaceConstraintDtoMappingAware
{
    public function toDto(): LearnplaceConstraint
    {
        /**
         * @var LearnplaceConstraintDtoMappingAware|LearnplaceConstraintModel $this
         */
        $dto = new LearnplaceConstraint();
        $dto->setPreviousLearnplace($this->getPreviousLearnplace()->toDto())
            ->setId($this->getId());

        return $dto;
    }
}

/**
 * Trait LearnplaceConstraintModelMappingAware
 *
 * Adds the functionality to map learnplace constraint dto to learnplace constraint model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait LearnplaceConstraintModelMappingAware
{
    public function toModel(): LearnplaceConstraintModel
    {
        /**
         * @var LearnplaceConstraintDtoMappingAware|LearnplaceConstraint $this
         */
        $model = new LearnplaceConstraintModel();
        $model->setPreviousLearnplace($this->getPreviousLearnplace()->toModel())
            ->setId($this->getId());

        return $model;
    }
}
