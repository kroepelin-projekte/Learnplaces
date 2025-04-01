<?php

declare(strict_types=1);

use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\security\AccessGuard;
use ILIAS\DI\UIServices;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\gui\VisitorsTable;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\api\Database\OAuthEntity;

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
        $query = $DIC->http()->wrapper()->query();
        $string = $DIC->refinery()->kindlyTo()->string();

        if (!$query->has('state')) {
            throw new \Exception('Permission Denied');
        }

        $state = $query->retrieve('state', $string);

        if ($DIC->user()->isAnonymous()) {
            $target = 'xsrl_lernorte-auth_' . $state;
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
        global $DIC;
        $query = $DIC->http()->wrapper()->query();
        $string = $DIC->refinery()->kindlyTo()->string();

        if (!$query->has('state')) {
            throw new \Exception('Permission Denied');
        }

        $state = $query->retrieve('state', $string);

        $record = OAuthEntity::where(['state' => $state])->first();
        if (!$record) {
            throw new \Exception('Permission Denied');
        }

        $redirect_uri = $record->getRedirectUri();
        $redirect_uri = $this->urlsafe_base64_decode($redirect_uri);
        $code_challenge = $record->getCodeChallenge();
        $expire = $record->getExpire();

        if (time() > $expire) {
            header("Location: $redirect_uri");
            exit;
        }

        $code = bin2hex(random_bytes(32));

        $record
            ->setCode($code)
            ->update();

        $uri = "$redirect_uri?code=$code&state=$state";

        header("Location: $uri");
        exit;
    }

    /**
     * @param $input
     * @return false|string
     */
    private function urlsafe_base64_decode($input) {
        $replaced = str_replace(['-', '_'], ['+', '/'], $input);

        $padding = strlen($replaced) % 4;
        if ($padding > 0) {
            $replaced .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($replaced);
    }
}