<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\container\exception;

use RuntimeException;
use SRAG\Learnplaces\container\PluginContainer;

/**
 * Class DependencyResolvementException
 *
 * Indicates that the requested class or one of its dependencies can not be resolved by the PluginContainer.
 *
 *
 * @package SRAG\Learnplaces\container\exception
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @see PluginContainer
 */
class DependencyResolutionException extends RuntimeException
{
}
