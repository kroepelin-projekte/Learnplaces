<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\persistence\dto;

use SRAG\Lernplaces\persistence\mapping\PictureModelMappingAware;

/**
 * Class Picture
 *
 * @package SRAG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class Picture
{
    use PictureModelMappingAware;

    private int $id = 0;

    private string $resourceId;

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
     * @return Picture
     */
    public function setId(int $id): Picture
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
     * @return Picture
     */
    public function setResourceId(string $resourceId): Picture
    {
        $this->resourceId = $resourceId;

        return $this;
    }
}
