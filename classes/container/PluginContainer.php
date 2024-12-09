<?php

declare(strict_types=1);

namespace KPG\Learnplaces\container;

use ILIAS\DI\Container;
use InvalidArgumentException;
use Pimple\ServiceProviderInterface;
use KPG\Learnplaces\container\exception\DependencyResolutionException;
use KPG\Learnplaces\container\provider\v54\BlockServiceProvider;
use KPG\Learnplaces\container\provider\v54\GUIProvider;
use KPG\Learnplaces\container\provider\v54\HttpServiceProvider;
use KPG\Learnplaces\container\provider\v54\MediaServiceProvider;
use KPG\Learnplaces\container\provider\v54\PluginProvider;
use KPG\Learnplaces\container\provider\v54\RepositoryProvider;
use KPG\Learnplaces\container\provider\v54\SecurityServiceProvider;
use KPG\Learnplaces\container\provider\v54\ViewProvider;
use KPG\Learnplaces\container\provider\v54\VisibilityServiceProvider;

/**
 * Class PluginContainer
 *
 * The plugin service container, which is responsible for the
 * bootstrap process of the underlying pimple container which is provided by ILIAS.
 *
 * @package KPG\Learnplaces\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PluginContainer
{
    /**
     * @var ServiceProviderInterface[] $provider
     */
    private static $provider54 = [
        PluginProvider::class,
        RepositoryProvider::class,
        MediaServiceProvider::class,
        BlockServiceProvider::class,
        GUIProvider::class,
        ViewProvider::class,
        VisibilityServiceProvider::class,
        HttpServiceProvider::class,
        SecurityServiceProvider::class,

        //Add new service provider here
    ];

    /**
     * @var ServiceProviderInterface[] $provider
     */
    private static $provider6 = [
        PluginProvider::class,
        RepositoryProvider::class,
        MediaServiceProvider::class,
        BlockServiceProvider::class,
        \KPG\Learnplaces\container\provider\v6\GUIProvider::class,
        ViewProvider::class,
        VisibilityServiceProvider::class,
        HttpServiceProvider::class,
        SecurityServiceProvider::class,

        //Add new service provider here
    ];

    /**
     * @var Container $container
     */
    private static $container;

    /**
     * Bootstraps the plugin dependency container, with all service providers.
     * This method requires an registered autoloader and
     * the already bootstrapped ILIAS DI container.
     *
     * @return void
     */
    public static function bootstrap(): void
    {
        if (!isset($GLOBALS['DIC'])) {
            return;
        }

        static::$container = $GLOBALS['DIC'];

        if (version_compare(ILIAS_VERSION_NUMERIC, "6.0", "<")) {
            foreach (static::$provider54 as $providerClass) {
                $provider = new $providerClass();
                static::$container->register($provider);
            }
        } else {
            foreach (static::$provider6 as $providerClass) {
                $provider = new $providerClass();
                static::$container->register($provider);
            }
        }
    }

    /**
     * @param string $class
     * @return object
     */
    public static function resolve(string $class): object
    {
        if(!static::$container->offsetExists($class)) {
            throw new DependencyResolutionException("The class \"$class\" was not found.");
        }

        return static::$container[$class];
    }
}
