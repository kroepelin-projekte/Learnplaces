<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\settings;

/**
 * Class SettingModel
 *
 * @package KPG\Learnplaces\gui\settings
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class SettingModel
{
    private string $title = "";
    private string $description = "";
    private bool $online = false;
    private string $defaultVisibility = "ALWAYS";
    private float $latitude = 0.0;
    private float $longitude = 0.0;
    private float $elevation = 0.0;
    private int $radius = 200;
    private int $mapZoom = 0;
    private ?string $tags = null;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return SettingModel
     */
    public function setTitle(string $title): SettingModel
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return SettingModel
     */
    public function setDescription(string $description): SettingModel
    {
        $this->description = $description;

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
     * @return SettingModel
     */
    public function setOnline(bool $online): SettingModel
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
     * @return SettingModel
     */
    public function setDefaultVisibility(string $defaultVisibility): SettingModel
    {
        $this->defaultVisibility = $defaultVisibility;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     *
     * @return SettingModel
     */
    public function setLatitude(float $latitude): SettingModel
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     *
     * @return SettingModel
     */
    public function setLongitude(float $longitude): SettingModel
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getElevation(): float
    {
        return $this->elevation;
    }

    /**
     * @param float $elevation
     *
     * @return SettingModel
     */
    public function setElevation(float $elevation): SettingModel
    {
        $this->elevation = $elevation;

        return $this;
    }

    /**
     * @return int
     */
    public function getRadius(): int
    {
        return $this->radius;
    }

    /**
     * @param int $radius
     *
     * @return SettingModel
     */
    public function setRadius(int $radius): SettingModel
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * @return int
     */
    public function getMapZoom(): int
    {
        return $this->mapZoom;
    }

    /**
     * @param int $mapZoom
     *
     * @return SettingModel
     */
    public function setMapZoom(int $mapZoom): SettingModel
    {
        $this->mapZoom = $mapZoom;

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
    public function setTags(?string $tags): SettingModel
    {
        $this->tags = $tags;

        return $this;
    }
}
