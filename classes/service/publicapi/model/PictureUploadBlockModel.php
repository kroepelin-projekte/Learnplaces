<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\PictureUploadBlockDtoMappingAware;

/**
 * Class PictureUploadBlock
 *
 * @package KPG\Learnplaces\service\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureUploadBlockModel extends BlockModel
{
    use PictureUploadBlockDtoMappingAware;
}
