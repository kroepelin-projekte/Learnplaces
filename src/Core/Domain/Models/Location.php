<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

class Location extends IliasObject
{
    public string $id;
    public string $title;
    public string $objectType;
    public string $latitude;
    public string $longitude;
    public string $radius;
    public int $zoom;

    public static function new(
        string $parentRefId,
        string $title,
        string $objectType,
        string $latitude,
        string $longitude,
        int $radius,
        int $zoom
    ) : self {
        return new self($parentRefId, $title, $objectType, $latitude, $longitude, $radius, $zoom);
    }

    private function __construct(
        string $parentRefId,
        string $title,
        string $objectType,
        string $latitude,
        string $longitude,
        int $radius,
        int $zoom
    ) {
        $this->id = "parentRefId/".$parentRefId;
        $this->title = $title;
        $this->objectType = $objectType;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->radius = $radius;
        $this->zoom = $zoom;
    }
}