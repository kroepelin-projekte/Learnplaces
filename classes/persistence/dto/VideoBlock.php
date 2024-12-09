<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\VideoBlockModelMappingAware;

/**
 * Class VideoBlock
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class VideoBlock extends Block
{
    use VideoBlockModelMappingAware;

    /**
     * @var string $path
     */
    private $path = "";
    /**
     * @var string $coverPath
     */
    private $coverPath = "";
    private string $resourceId;


    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * @param string $path
     *
     * @return VideoBlock
     */
    public function setPath(string $path): VideoBlock
    {
        $this->path = $path;

        return $this;
    }


    /**
     * @return string
     */
    public function getCoverPath(): string
    {
        return $this->coverPath;
    }


    /**
     * @param string $coverPath
     *
     * @return VideoBlock
     */
    public function setCoverPath(string $coverPath): VideoBlock
    {
        $this->coverPath = $coverPath;

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
     * @return $this
     */
    public function setResourceId(string $resourceId): VideoBlock
    {
        $this->resourceId = $resourceId;
        return $this;
    }
}
