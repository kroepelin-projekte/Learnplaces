<?php

namespace KPG\Learnplaces\service\publicapi\model;

/**
 * Interface BlockConstraint
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
interface BlockConstraint
{
    /**
     * @return int
     */
    public function getId(): int;


    /**
     * @param int $id
     *
     * @return BlockConstraint
     */
    public function setId(int $id): BlockConstraint;


    /**
     * Maps the block constraint model into a block constraint dto.
     *
     * @return \KPG\Learnplaces\persistence\dto\BlockConstraint
     */
    public function toDto();
}
