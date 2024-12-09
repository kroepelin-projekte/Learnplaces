<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\model;

use KPG\Lernplaces\persistence\mapping\MapBlockDtoMappingAware;

/**
 * Class MapBlock
 *
 * @package KPG\Learnplaces\service\publicapi\model
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class MapBlockModel extends BlockModel
{
    use MapBlockDtoMappingAware;
}
