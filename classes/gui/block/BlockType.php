<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block;

/**
 * Interface BlockType
 *
 * @package KPG\Learnplaces\gui\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
interface BlockType
{
    public const PICTURE_UPLOAD = 1;
    public const MAP = 2;
    public const PICTURE = 3;
    public const ILIAS_LINK = 4;
    public const ACCORDION = 5;
    public const RICH_TEXT = 6;
    public const VIDEO = 7;
}
