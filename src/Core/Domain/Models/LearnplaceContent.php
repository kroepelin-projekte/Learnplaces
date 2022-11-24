<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

class LearnplaceContent extends IliasObject
{
    public string $id;
    public string $title;
    public string $content;
    public string $contentType;
    public string $visibility;

    public static function new(
        int $blockId,
        string $content,
        string $content_type,
        string $visibility
    ) : self {
        //todo crypt the content

        return new self($blockId, substr($content, 0, 50) . "...", $content, $content_type, $visibility);
    }

    private function __construct(
        int $blockId,
        string $title,
        string $content,
        string $content_type,
        string $visibility
    ) {
        $this->id = "xsrlBlockId/" . $blockId;
        $this->title = $title;
        $this->content = $content;
        $this->contentType = $content_type;
        $this->visibility = $visibility;
    }
}