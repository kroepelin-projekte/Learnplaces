<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use KPG\Learnplaces\persistence\dto\Block;

/**
 * Trait BlockConstraintAware
 *
 * The block constraint aware trait provides a convenience functionality to store the
 * foreign key of the constraint.
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait BlockConstraintAware
{
    /**
     * Stores the underlying relation of the constraint with the block.
     *
     * @param Block $block The block which has a constraint which should be associated with the constraint.
     *
     * @return void
     */
    private function storeBlockConstraint(Block $block)
    {
        $constraint = $block->getConstraint();

        if(!is_null($constraint)) {
            $constraintClass = get_class($constraint);

            /**
             * @var \KPG\Learnplaces\persistence\entity\PictureUploadBlock $constraintEntity
             */
            $constraintEntity = new $constraintClass($constraint->getId());
            $constraintEntity->setFkBlockId($block->getId());
            $constraintEntity->update();
        }
    }

}
