<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\LearnplaceConstraintModelMappingAware;

/**
 * Class LearnplaceConstraint
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class LearnplaceConstraint implements BlockConstraint
{
    use LearnplaceConstraintModelMappingAware;

    /**
     * @var int $id
     */
    private $id = 0;
    /**
     * @var Learnplace $previousLearnplace
     */
    private $previousLearnplace;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @param int $id
     *
     * @return BlockConstraint
     */
    public function setId(int $id): BlockConstraint
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @return Learnplace
     */
    public function getPreviousLearnplace(): Learnplace
    {
        return $this->previousLearnplace;
    }


    /**
     * @param Learnplace $previousLearnplace
     *
     * @return LearnplaceConstraint
     */
    public function setPreviousLearnplace(Learnplace $previousLearnplace): LearnplaceConstraint
    {
        $this->previousLearnplace = $previousLearnplace;

        return $this;
    }

}
