<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

use fluxlabs\learnplaces\Adapters\Api\IdValue;

class Learnplace
{
    public int $ref_id;
    public int $obj_id;
    public IdValue $title;
    public IdValue $description;

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
        $this->title = IdValue::new('xsrl_id', $ref_id, $title);
        $this->description =  IdValue::new('xsrl_id', $ref_id, $description);
    }
}