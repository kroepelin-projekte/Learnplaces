<?php

namespace KPG\Learnplaces\service\validation\exception;

use RuntimeException;

/**
 * Class ValidationException
 *
 * Used to indicate that the data validation failed.
 * This exception can be used service wide.
 *
 * @package KPG\Learnplaces\service\validation\exception
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class ValidationException extends RuntimeException
{
}
