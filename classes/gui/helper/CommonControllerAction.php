<?php

namespace SRAG\Learnplaces\gui\helper;

/**
 * Interface CommonControllerAction
 *
 * @package SRAG\Learnplaces\gui\helper
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
interface CommonControllerAction
{
    public const CMD_INDEX = "index";
    public const CMD_ADD = "add";
    public const CMD_CREATE = "create";
    public const CMD_EDIT = "edit";
    public const CMD_UPDATE = "update";
    public const CMD_CONFIRM = "confirm";
    public const CMD_DELETE = "delete";
    public const CMD_CANCEL = "cancel";
}
