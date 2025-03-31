<?php

declare(strict_types=1);

use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\security\AccessGuard;
use ILIAS\DI\UIServices;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\gui\VisitorsTable;
use KPG\Learnplaces\container\PluginContainer;

/**
 * @ilCtrl_isCalledBy xsrlAuthGUI: ilUIPluginRouterGUI
 */
class xsrlAuthGUI
{
    public const CMD_AUTH = 'auth';

    /**
     * @return void
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function executeCommand(): void
    {
        global $DIC;

        if ($DIC->user()->isAnonymous()) {
            $target = 'xsrl_lernorte-auth';
            $DIC->ctrl()->redirectToURL('login.php?target=' . $target . '&cmd=force_login');
        }

        switch ($cmd = $DIC->ctrl()->getCmd()) {
            case self::CMD_AUTH:
                $this->$cmd();
                break;
        }
    }

    /**
     * @return void
     */
    private function auth(): void
    {
        // todo wenn an dieser Stelle kein Zwischenspeicher ist dann abbrechen.

        $code = bin2hex(random_bytes(32));

        // todo code mit den anderen daten von der auth route zwischenspeichern.

        $state = 'vom_zwischenspeicher';

        // todo hier die redirect_uri vom zwischenspeicher benutzen
        $redirect_uri = 'http://localhost:5173/auth_callback';

        $uri = urlencode("$redirect_uri?code=$code&state=$state");

        // todo code und state vom zwischenspeicher mit zurückschicken
        header("Location: $uri");
        exit;
    }
}