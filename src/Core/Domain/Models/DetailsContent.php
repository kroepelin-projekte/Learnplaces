<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

class DetailsContent
{
    public ?string $parentId;
    public string $id;
    public string $name = 'details';
    public string $type = 'object';
    public string $value;
    public array $contentList;
    public string $visibility;

    public static function new(
        ?string $parentId = null,
        string $id,
        string $value,
        string $visibility,
        array $contentList = []
    ) : Object {
        return new self(
            $parentId, $id,$value, $visibility, $contentList
        );
    }


    private function __construct(
        ?string $parentId = null,
        string $id,
        string $value,
        string $visibility,
        array $contentList
    ) {
        $this->parentId = $parentId;
        $this->id =  $id; //"xsrlBlockId/" . $blockId;
        $this->value = $value;
        $this->visibility = $visibility;
        $this->contentList = $contentList;
    }
}