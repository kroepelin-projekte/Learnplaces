<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\persistence\entity;

use ActiveRecord;

use function intval;

/**
 * Class ILIASLinkBlock
 *
 * @package SRAG\Learnplaces\persistence\entity
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class ILIASLinkBlock extends ActiveRecord
{
    /**
     * @return string
     */
    public static function returnDbTableName(): string
    {
        return 'xsrl_ilias_link_block';
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
     * @var int
     *
     * @con_has_field  true
     * @con_is_notnull true
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $ref_id = 0;

    /**
     * @var int|null
     *
     * @con_has_field  true
     * @con_is_unique  true
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
     * @return ILIASLinkBlock
     */
    public function setPkId(int $pk_id): ILIASLinkBlock
    {
        $this->pk_id = $pk_id;

        return $this;
    }


    /**
     * @return int
     */
    public function getRefId(): int
    {
        return intval($this->ref_id);
    }


    /**
     * @param int $ref_id
     *
     * @return ILIASLinkBlock
     */
    public function setRefId(int $ref_id): ILIASLinkBlock
    {
        $this->ref_id = $ref_id;

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
     * @return ILIASLinkBlock
     */
    public function setFkBlockId($fk_block_id)
    {
        $this->fk_block_id = $fk_block_id;

        return $this;
    }
}
