<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\persistence\entity;

use ActiveRecord;

use function intval;

/**
 * Class LearnplaceConstraint
 *
 * @package SRAG\Learnplaces\persistence\entity
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class LearnplaceConstraint extends ActiveRecord
{
    /**
     * @return string
     */
    public static function returnDbTableName(): string
    {
        return 'xsrl_learnplace_constr'; //xsrl_learnplace_constraint
    }

    /**
     * @var int
     *
     * @con_is_primary true
     * @con_sequence   true
     * @con_is_unique  true
     * @con_has_field  true
     * @con_is_notnull true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $pk_id = 0;
    /**
     * @var int|null
     *
     * @con_has_field  true
     * @con_is_notnull false
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $fk_previous_learnplace = null;
    /**
     * @var int|null
     *
     * @con_has_field  true
     * @con_is_notnull false
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $fk_block_id = null;


    /**
     * @return int
     */
    public function getPkId(): int
    {
        return intval($this->pk_id);
    }


    /**
     * @param int $pk_id
     *
     * @return LearnplaceConstraint
     */
    public function setPkId(int $pk_id): LearnplaceConstraint
    {
        $this->pk_id = $pk_id;

        return $this;
    }


    /**
     * @return int|null
     */
    public function getFkPreviousLearnplace()
    {
        return is_null($this->fk_previous_learnplace) ? null : intval($this->fk_previous_learnplace);
    }


    /**
     * @param int|null $fk_previous_learnplace
     *
     * @return LearnplaceConstraint
     */
    public function setFkPreviousLearnplace($fk_previous_learnplace)
    {
        $this->fk_previous_learnplace = $fk_previous_learnplace;

        return $this;
    }


    /**
     * @return int|null
     */
    public function getFkBlockId()
    {
        return is_null($this->fk_block_id) ? null : intval($this->fk_block_id);
    }


    /**
     * @param int|null $fk_block_id
     *
     * @return LearnplaceConstraint
     */
    public function setFkBlockId($fk_block_id)
    {
        $this->fk_block_id = $fk_block_id;

        return $this;
    }
}
