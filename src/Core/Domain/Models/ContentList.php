<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

class ContentList
{
    public ?string $parentId;
    public string $id;
    public string $name = 'contentList';
    public string $type = 'array';
    public array $value;
    public string $visibility;

    public static function new(
        ?string $parentId = null,
        string $id,
        array $value,
        string $visibility
    ) : Object {
        return new self(
            $parentId, $id,$value, $visibility
        );
    }


    private function __construct(
        ?string $parentId = null,
        string $id,
        array $value,
        string $visibility
    ) {
        $this->parentId = $parentId;
        $this->id =  $id; //"xsrlBlockId/" . $blockId;
        $this->value = $value;
        $this->visibility = $visibility;
    }
}