<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\HorizontalLineBlockDtoMappingAware;

/**
 * Class HorizontalLineBlock
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class HorizontalLineBlockModel extends BlockModel
{
    use HorizontalLineBlockDtoMappingAware;
}
