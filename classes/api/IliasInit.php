<?php

namespace KPG\Learnplaces\api;

use ilContext;
use ilInitialisation;

if (PHP_SAPI === 'cli') {
    return;
}

chdir("../../../../../../../../../");

include_once "Services/Context/classes/class.ilContext.php";
include_once "Services/Init/classes/class.ilInitialisation.php";

class IliasInit extends ilInitialisation
{
    /**
     * @description create Ilias Instance
     * @return void
     */
    public static function init()
    {
        ilContext::init(ilContext::CONTEXT_REST);
        ilInitialisation::initILIAS();
        self::initGlobal('ilUser', 'ilObjUser', './Services/User/classes/class.ilObjUser.php');
        global $DIC;
        self::initAccessibilityControlConcept($DIC);
        self::initAccessHandling();
    }
}
