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
     * @throws ilCtrlException
     */
    public function performCommand(string $cmd): void
    {
        $config_controller = new ConfigController();
        $config_controller->performCMD($cmd);
    }
}