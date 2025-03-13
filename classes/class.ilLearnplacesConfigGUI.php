<?php

use Repository\RepositoryObject\Learnplaces\classes\api\Config\ConfigController;
/**
 * @ilCtrl_IsCalledBy ilLearnplacesConfigGUI: ilObjComponentSettingsGUI
 */
class ilLearnplacesConfigGUI extends ilPluginConfigGUI
{
    /**
     * @param string $cmd
     * @return void
     */
    public function performCommand(string $cmd): void
    {
        //KPG\Learnplaces\api\Database\Tables\CookieSecrets::install();
        $config_controller = new ConfigController();
        $config_controller->performCMD($cmd);
    }
}