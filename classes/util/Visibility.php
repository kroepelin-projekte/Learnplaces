<?php

namespace SRAG\Learnplaces\util;

/**
 * Interface Visibility
 *
 * Lists all valid visibilities for the learnplace blocks.
 *
 * @package SRAG\Learnplaces\util
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
interface Visibility
{
    /**
     * The block is always visible.
     */
    public const ALWAYS = "ALWAYS";
    /**
     * The block is never visible.
     */
    public const NEVER = "NEVER";
    /**
     * The block is only temporary visible at the learnplace location.
     */
    public const ONLY_AT_PLACE = "ONLY_AT_PLACE";
    /**
     * The block must be unlocked with a visit of a place, but
     * is permanently visible afterwards.
     */
    public const AFTER_VISIT_PLACE = "AFTER_VISIT_PLACE";
    /**
     * First an other place has to be visited to unlock the block permanently.
     */
    public const AFTER_VISIT_OTHER_PLACE = "AFTER_VISIT_OTHER_PLACE";
}
