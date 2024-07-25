<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\persistence\entity;

use ActiveRecord;

/**
 * Class Picture
 *
 * @package SRAG\Learnplaces\persistence\entity
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class Picture extends ActiveRecord
{
    /**
     * @return string
     */
    public static function returnDbTableName(): string
    {
        return 'xsrl_picture';
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
     * @return int
     */
    public function getPkId(): int
    {
        return intval($this->pk_id);
    }

    /**
     * @param int $pk_id
     *
     * @return Picture
     */
    public function setPkId(int $pk_id): Picture
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
     *
     * @return Picture
     */
    public function setResourceId(string $resource_id): Picture
    {
        $this->resource_id = $resource_id;

        return $this;
    }
}
