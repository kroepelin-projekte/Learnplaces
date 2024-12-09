<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\dto;

use KPG\Lernplaces\persistence\mapping\HorizontalLineBlockModelMappingAware;

/**
 * Class HorizontalLineBlock
 *
 * @package KPG\Lernplaces\persistence\dto
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class HorizontalLineBlock extends Block
{
    use HorizontalLineBlockModelMappingAware;
}
