<?php

namespace KPG\Learnplaces\service\visibility;

use KPG\Learnplaces\service\publicapi\block\LearnplaceService;

/**
 * Interface LearnplaceServiceDecorator
 *
 * The interface which all visibility decorator must implement.
 * The responsibility of the visibility decorators is to filter the blocks which are not visible for the user.
 *
 * @package KPG\Learnplaces\service\visibility
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @internal
 */
interface LearnplaceServiceDecorator extends LearnplaceService
{
}
