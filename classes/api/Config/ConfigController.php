<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Config;

use ilLearnplacesPlugin;
use ILIAS\DI\Container;
use ilLearnplacesConfigGUI;
use ilCtrlException;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\API\APIController;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Permission\PermissionController;

class ConfigController implements constConfig
{
    private \ilLearnplacesPlugin $plugin;
    private Container $DIC;

    public function __construct()
    {
        $this->plugin = ilLearnplacesPlugin::getInstance();
        global $DIC;
        $this->DIC = $DIC;
    }

    /**
     * @throws ilCtrlException
     */
    public function performCMD($cmd): void
    {
        $this->initTabs();
        $this->activateTab(self::TAB_ID_API_SETTINGS);
        $this->initSubTabs();
        switch ($cmd) {
            case self::CMD_SHOW_API_SETTINGS:
            case self::CMD_SAVE_API_SETTINGS:
            case self::CMD_REFRESH_SECRET:
            case "configure":
                $this->activateSubTab(self::TAB_SUB_ID_API_SETTINGS);
                $controller = new APIController($this->plugin, $this->DIC);
                $controller->performCMD($cmd);
                break;
            case self::CMD_SHOW_PERMISSION_SETTINGS:
            case self::CMD_SAVE_PERMISSION_SETTINGS:
                $this->activateSubTab(self::TAB_SUB_ID_PERMISSION_SETTINGS);
                $controller = new PermissionController($this->plugin, $this->DIC);
                $controller->performCMD($cmd);
                break;
        }
    }

    /**
     * @throws ilCtrlException
     */
    private function initTabs(): void
    {
        $this->DIC->tabs()->addTab(
            self::TAB_ID_API_SETTINGS, $this->plugin->txt(self::LANG_TAB_API_SETTINGS),
            $this->DIC->ctrl()->getLinkTargetByClass(ilLearnplacesConfigGUI::class, self::CMD_SHOW_API_SETTINGS)
        );
    }

    /**
     * @throws ilCtrlException
     */
    private function initSubTabs(): void
    {
        $this->DIC->tabs()->addSubTab(
            self::TAB_SUB_ID_API_SETTINGS, $this->plugin->txt(self::LANG_TAB_SUB_API_SETTINGS),
            $this->DIC->ctrl()->getLinkTargetByClass(ilLearnplacesConfigGUI::class, self::CMD_SHOW_API_SETTINGS)
        );
        $this->DIC->tabs()->addSubTab(
            self::TAB_SUB_ID_PERMISSION_SETTINGS, $this->plugin->txt(self::LANG_TAB_SUB_PERMISSION_SETTINGS),
            $this->DIC->ctrl()->getLinkTargetByClass(ilLearnplacesConfigGUI::class, self::CMD_SHOW_PERMISSION_SETTINGS)
        );
    }

    private function activateTab(string $id): void
    {
        $this->DIC->tabs()->activateTab($id);
    }

    private function activateSubTab(string $id): void
    {
        $this->DIC->tabs()->activateSubTab($id);
    }

}