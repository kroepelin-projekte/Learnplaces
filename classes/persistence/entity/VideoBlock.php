<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\persistence\entity;

use ActiveRecord;

use function intval;

/**
 * Class VideoBlock
 *
 * @package SRAG\Learnplaces\persistence\entity
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class VideoBlock extends ActiveRecord
{
    /**
     * @return string
     */
    public static function returnDbTableName(): string
    {
        return 'xsrl_video_block';
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
     * @var string
     *
     * @con_is_notnull true
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     2000
     */
    protected $resource_id = "";
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
     * @return VideoBlock
     */
    public function setPkId(int $pk_id): VideoBlock
    {
        $this->pk_id = $pk_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resource_id;
    }

    /**
     * @param string $resource_id
     * @return $this
     */
    public function setResourceId(string $resource_id): VideoBlock
    {
        $this->resource_id = $resource_id;

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
     * @return VideoBlock
     */
    public function setFkBlockId($fk_block_id)
    {
        $this->fk_block_id = $fk_block_id;

        return $this;
    }
}
