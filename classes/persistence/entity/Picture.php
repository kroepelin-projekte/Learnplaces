<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\persistence\entity;

use ActiveRecord;
use SRAG\Learnplaces\service\filesystem\FileMigration;

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
    protected $original_path = "";
    /**
     * @var string
     *
     * @con_is_notnull true
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     2000
     */
    protected $preview_path = "";
    /**
     * @var string
     *
     * @con_is_notnull false
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
    public function getOriginalPath(): string
    {
        return $this->original_path;
    }


    /**
     * @param string $original_path
     *
     * @return Picture
     */
    public function setOriginalPath(string $original_path): Picture
    {
        $this->original_path = $original_path;

        return $this;
    }


    /**
     * @return string
     */
    public function getPreviewPath(): string
    {
        return $this->preview_path;
    }


    /**
     * @param string $preview_path
     *
     * @return Picture
     */
    public function setPreviewPath(string $preview_path): Picture
    {
        $this->preview_path = $preview_path;

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
    public function setResourceId(string $resource_id): Picture
    {
        $this->resource_id = $resource_id;
        return $this;
    }

    /**
     * Update from ILIAS 7 to 8
     *
     * @return void
     */
    public static function updateDB4(): void
    {
        global $DIC;
        $db = $DIC->database();

        if (! $db->tableColumnExists('xsrl_picture', 'resource_id')) {
            $db->addTableColumn('xsrl_picture', 'resource_id', [
                'type' => 'text',
                'length' => 2000,
                'notnull' => false,
                'default' => ''
            ]);
        }
    }
}
