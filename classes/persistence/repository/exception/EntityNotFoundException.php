<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository\exception;

use RuntimeException;
use Throwable;

/**
 * Class EntityNotFoundException
 *
 * Thrown if no entity was found by the dao abstraction.
 *
 * @package KPG\Learnplaces\persistence\dao\exception
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class EntityNotFoundException extends RuntimeException
{
    /**
     * EntityNotFoundException constructor.
     *
     * @param string    $message  The exception message which describes the cause of the exception.
     * @param Throwable $previous The exception which happened before the current one.
     */
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
