<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

class Course
{
    public int $ref_id;
    public int $obj_id;
    public string $title;
    public string $description;

    public static function new(
        int $ref_id,
        int $obj_id,
        string $title,
        string $description
    ) : self {
        return new self($ref_id, $obj_id, $title, $description);
    }

    private function __construct(
        int $ref_id,
        int $obj_id,
        string $title,
        string $description
    ) {
        $this->ref_id = $ref_id;
        $this->obj_id = $obj_id;
        $this->title = $title;
        $this->description = $description;
    }
}