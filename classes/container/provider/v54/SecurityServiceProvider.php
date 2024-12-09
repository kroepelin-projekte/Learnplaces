<?php

declare(strict_types=1);

namespace KPG\Learnplaces\container\provider\v54;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\security\AccessGuard;
use KPG\Learnplaces\service\security\AccessGuardImpl;
use KPG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactory;

/**
 * Class SecurityServiceProvider
 *
 * Service provider for the security namespace KPG\Learnplaces\service\security.
 *
 * @package KPG\Learnplaces\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class SecurityServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[AccessGuard::class] = function ($c) {
            return new AccessGuardImpl(
                $c[ServerRequestInterface::class],
                $c[LearnplaceService::class],
                $c[LearnplaceServiceDecoratorFactory::class],
                $c['ilAccess']
            );
        };
    }
}
