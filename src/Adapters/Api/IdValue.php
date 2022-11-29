<?php

namespace fluxlabs\learnplaces\Adapters\Api;

class IdValue {
    public string $type;
    public string $id;
    public string $value;


    public static function new(
        string $idType,
        string $id,
        string $value
    ): self {
        $obj = new self();
        $obj->idType = $idType;
        $obj->onClick = $idType."/".$id;
        $obj->id = $id;
        $obj->value = $value;
        return $obj;
    }

    private function construct() {

    }
}