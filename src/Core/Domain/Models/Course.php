<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

use fluxlabs\learnplaces\Adapters\Api\IdValue;

class Course extends IliasObject
{
    public string $id;
    public int $objId;
    public string $title;
    public string $description;
    public string $objectType = "crs";

    public static function new(
        int $refId,
        int $objId,
        string $title,
        $description
    ) : self {
        return new self($refId, $objId, $title, $description);
    }

    private function __construct(
        int $refId,
        int $objId,
        string $title,
        $description
    ) {
        $this->id ="refId/".$refId;
        $this->objId = $objId;
        $this->title = $title;
        $this->description = "";
    }
}