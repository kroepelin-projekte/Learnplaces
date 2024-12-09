<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\util;

use ilCtrl;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\gui\component\PlusView;

use function array_key_exists;

/**
 * Trait InsertPositionAware
 *
 * @package KPG\Learnplaces\gui\block\util
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait InsertPositionAware
{
    /**
     * Fetch the requested insert position of the new block.
     *
     * @param string[] $queries Query parameters.
     *
     * @return int  The zero based insert index.
     *
     * @see PlusView::POSITION_QUERY_PARAM  The query param which is searched by this method.
     */
    private function getInsertPosition(array $queries): int
    {
        $position = 0;
        if(!array_key_exists(PlusView::POSITION_QUERY_PARAM, $queries)) {
            return $position;
        }

        $position = intval($queries[PlusView::POSITION_QUERY_PARAM]);
        $position = $position >= 0 ? $position : 0;
        return $position;
    }
}
