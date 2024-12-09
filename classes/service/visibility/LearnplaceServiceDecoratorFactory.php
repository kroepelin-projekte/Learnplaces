<?php

namespace KPG\Learnplaces\service\visibility;

use KPG\Learnplaces\service\publicapi\block\LearnplaceService;

/**
 * Interface LearnplaceDecoratorFactory
 *
 * @package KPG\Learnplaces\service\visibility
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
interface LearnplaceServiceDecoratorFactory
{
    /**
     * Decorate the service with the visibility filter decorators.
     * Make sure to NEVER save something about the decorated services, this could lead to
     * unexpected behaviours.
     *
     * @param LearnplaceService $learnplaceService
     *
     * @return LearnplaceService
     */
    public function decorate(LearnplaceService $learnplaceService): LearnplaceService;
}
