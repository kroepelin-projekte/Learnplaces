<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\PictureDtoMappingAware;

/**
 * Class Picture
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureModel
{
    use PictureDtoMappingAware;

    private int $id = 0;

    private string $resourceId = "";

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
     * @return PictureModel
     */
    public function setId(int $id): PictureModel
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @param string $resourceId
     *
     * @return PictureModel
     */
    public function setResourceId(string $resourceId): PictureModel
    {
        $this->resourceId = $resourceId;

        return $this;
    }
}
