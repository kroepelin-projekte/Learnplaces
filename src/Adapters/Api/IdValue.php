<?php

namespace fluxlabs\learnplaces\Adapters\Api;

class IdValue {
    public int $id;
    public string $value;

    public static function new(
        int $id,
        string $value
    ): self {
        $obj = new self();
        $obj->id = $id;
        $obj->value = $value;
        return $obj;
    }

    private function construct() {

    }
}