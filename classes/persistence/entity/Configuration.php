<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\entity;

use ActiveRecord;

use function intval;

/**
 * Class Configuration
 *
 * @package KPG\Learnplaces\persistence\entity
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class Configuration extends ActiveRecord
{
    /**
     * @return string
     */
    public static function returnDbTableName(): string
    {
        return 'xsrl_configuration';
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
     * @con_length     1
     */
    protected $object_online = 0;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_is_notnull true
     * @con_fieldtype  integer
     * @con_length     1
     */
    protected $map_zoom_level = 0;
    /**
     * @var int|null
     *
     * @con_has_field  true
     * @con_is_notnull false
     * @con_fieldtype  integer
     * @con_length     8
     */
    protected $fk_visibility_default = null;

    /**
     * @var string|null
     *
     * @con_has_field  true
     * @con_is_notnull false
     * @con_fieldtype  text
     * @con_length     1000
     */
    protected $tags = null;

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
     * @return Configuration
     */
    public function setPkId(int $pk_id): Configuration
    {
        $this->pk_id = $pk_id;

        return $this;
    }


    /**
     * @return int
     */
    public function getObjectOnline(): int
    {
        return intval($this->object_online);
    }


    /**
     * @param int $object_online
     *
     * @return Configuration
     */
    public function setObjectOnline(int $object_online): Configuration
    {
        $this->object_online = $object_online;

        return $this;
    }


    /**
     * @return int|null
     */
    public function getFkVisibilityDefault()
    {
        return is_null($this->fk_visibility_default) ? null : intval($this->fk_visibility_default);
    }


    /**
     * @param int|null $fk_visibility_default
     *
     * @return Configuration
     */
    public function setFkVisibilityDefault($fk_visibility_default)
    {
        $this->fk_visibility_default = $fk_visibility_default;

        return $this;
    }


    /**
     * @return int
     */
    public function getMapZoomLevel(): int
    {
        return intval($this->map_zoom_level);
    }


    /**
     * @param int $map_zoom_level
     *
     * @return Configuration
     */
    public function setMapZoomLevel(int $map_zoom_level): Configuration
    {
        $this->map_zoom_level = $map_zoom_level;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTags(): ?string
    {
        return $this->tags;
    }

    /**
     * @param string|null $tags
     * @return $this
     */
    public function setTags(?string $tags): Configuration
    {
        $this->tags = $tags;

        return $this;
    }
}
