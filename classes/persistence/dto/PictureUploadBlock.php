<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\PictureUploadBlockMappingAware;

/**
 * Class PictureUploadBlock
 *
 * @package KPG\Learnplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class PictureUploadBlock extends Block
{
    use PictureUploadBlockMappingAware;
}
