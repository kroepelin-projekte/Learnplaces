<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\util;

use ilAccessHandler;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\service\security\AccessGuard;

use function strcmp;

/**
 * Trait ReferenceIdAware
 *
 * @package KPG\Learnplaces\gui\block\util
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait ReferenceIdAware
{
    /**
     * @var ServerRequestInterface $request
     */
    private $request;

    /**
     * @return int
     */
    private function getCurrentRefId(): int
    {
        $queries = $this->request->getQueryParams();
        return intval($queries["ref_id"]);
    }
}
