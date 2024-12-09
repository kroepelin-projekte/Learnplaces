<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\exception;

use RuntimeException;

/**
 * Class ValidationException
 *
 * Indicates a failure of the form validation.
 * This exception is generally thrown by the FormView classes which validate the
 * received form content.
 *
 * @package KPG\Learnplaces\gui\exception
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class ValidationException extends RuntimeException
{
}
