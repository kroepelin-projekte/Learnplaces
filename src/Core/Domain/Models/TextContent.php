<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

class TextContent
{
    public ?string $parentId;
    public string $id;
    public string $name = 'text';
    public string $type = 'string';
    public string $value;
    public string $visibility;

    public static function new(
        ?string $parentId = null,
        string $id,
        string $value,
        string $visibility
    ) : Object {
        return new self(
            $parentId, $id,$value, $visibility
        );
    }


    private function __construct(
        ?string $parentId = null,
        string $id,
        string $value,
        string $visibility
    ) {
        $this->parentId = $parentId;
        $this->id =  $id; //"xsrlBlockId/" . $blockId;
        $this->value = $value;
        $this->visibility = $visibility;
    }
}