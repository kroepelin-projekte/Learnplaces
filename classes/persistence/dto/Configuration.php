<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\ConfigurationModelMappingAware;

/**
 * Class Configuration
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class Configuration
{
    use ConfigurationModelMappingAware;

    /**
     * @var int $id
     */
    private $id = 0;
    /**
     * @var bool $online
     */
    private $online = false;
    /**
     * @var string $defaultVisibility
     */
    private $defaultVisibility = "";
    /**
     * @var int $mapZoomLevel
     */
    private $mapZoomLevel = 0;

    /**
     * @var null|string $tags
     */
    private $tags = null;

    /**
     * @var string $qrCodeToken
     */
    private $qrCodeToken = '';

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
     * @return Configuration
     */
    public function setId(int $id): Configuration
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->online;
    }


    /**
     * @param bool $online
     *
     * @return Configuration
     */
    public function setOnline(bool $online): Configuration
    {
        $this->online = $online;

        return $this;
    }


    /**
     * @return string
     */
    public function getDefaultVisibility(): string
    {
        return $this->defaultVisibility;
    }


    /**
     * @param string $defaultVisibility
     *
     * @return Configuration
     */
    public function setDefaultVisibility(string $defaultVisibility): Configuration
    {
        $this->defaultVisibility = $defaultVisibility;

        return $this;
    }


    /**
     * @return int
     */
    public function getMapZoomLevel(): int
    {
        return $this->mapZoomLevel;
    }


    /**
     * @param int $mapZoomLevel
     *
     * @return Configuration
     */
    public function setMapZoomLevel(int $mapZoomLevel): Configuration
    {
        $this->mapZoomLevel = $mapZoomLevel;

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

    /**
     * @return string
     */
    public function getQrCodeToken(): string
    {
        return $this->qrCodeToken;
    }

    /**
     * @param string $qrCodeToken
     * @return $this
     */
    public function setQrCodeToken(string $qrCodeToken): Configuration
    {
        $this->qrCodeToken = $qrCodeToken;
        return $this;
    }
}
