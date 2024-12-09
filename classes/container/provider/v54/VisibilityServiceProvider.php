<?php

declare(strict_types=1);

namespace KPG\Learnplaces\container\provider\v54;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use KPG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactory;
use KPG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactoryImpl;

/**
 * Class VisibilityServiceProvider
 *
 * @package KPG\Learnplaces\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class VisibilityServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple[LearnplaceServiceDecoratorFactory::class] = function ($c) {return new LearnplaceServiceDecoratorFactoryImpl($c['ilUser']);};
    }
}
