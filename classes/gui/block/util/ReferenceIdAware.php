<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\util;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait ReferenceIdAware
 *
 * @package KPG\Learnplaces\gui\block\util
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait ReferenceIdAware
{
    private ServerRequestInterface $request;

    /**
     * @return int
     */
    private function getCurrentRefId(): int
    {
        $queries = $this->request->getQueryParams();
        return intval($queries["ref_id"]);
    }
}
