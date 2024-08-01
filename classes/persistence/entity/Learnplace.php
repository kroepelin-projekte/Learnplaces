<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\persistence\entity;

use ActiveRecord;

use function is_null;

/**
 * Class Learnplace
 *
 * @package SRAG\Learnplaces\persistence\entity
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class Learnplace extends ActiveRecord
{
    /**
     * @return string
     */
    public static function returnDbTableName(): string
    {
        return 'xsrl_learnplace';
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
    protected $fk_configuration = null;
    /**
     * @var int|null
     *
     * @con_has_field  true
     * @con_is_unique  true
     * @con_is_notnull false
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $fk_object_id = null;


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
     * @return Learnplace
     */
    public function setPkId(int $pk_id): Learnplace
    {
        $this->pk_id = $pk_id;

        return $this;
    }


    /**
     * @return int|null
     */
    public function getFkConfiguration()
    {
        return is_null($this->fk_configuration) ? null : intval($this->fk_configuration);
    }


    /**
     * @param int|null $fk_configuration
     *
     * @return Learnplace
     */
    public function setFkConfiguration($fk_configuration)
    {
        $this->fk_configuration = $fk_configuration;

        return $this;
    }


    /**
     * @return int|null
     */
    public function getFkObjectId()
    {
        return is_null($this->fk_object_id) ? null : intval($this->fk_object_id);
    }


    /**
     * @param int|null $fk_object_id
     *
     * @return Learnplace
     */
    public function setFkObjectId($fk_object_id)
    {
        $this->fk_object_id = $fk_object_id;

        return $this;
    }
}
