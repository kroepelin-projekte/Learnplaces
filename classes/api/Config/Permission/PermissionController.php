<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Config\Permission;

use Repository\RepositoryObject\Learnplaces\classes\api\Config\constConfig;
use ILIAS\DI\Container;

class PermissionController implements constConfig
{
    private \ilLearnplacesPlugin $plugin;
    private Container $DIC;
    private PermissionModel $model;
    private PermissionView $view;

    public function __construct(\ilLearnplacesPlugin $plugin, Container $DIC)
    {
        $this->plugin = $plugin;
        $this->DIC = $DIC;
        $this->view = new PermissionView($this->plugin, $this->DIC);
        $this->model = new PermissionModel($this->plugin, $this->DIC);
    }
    public function performCMD(string $cmd): void
    {
        switch ($cmd) {
            case self::CMD_SHOW_PERMISSION_SETTINGS:
                $this->showPermissionSettings();
                break;
            case self::CMD_SAVE_PERMISSION_SETTINGS:
                $this->savePermissionSettings();
                break;
        }
    }

    public function showPermissionSettings(): void
    {
        $panel = $this->DIC->ui()->factory()->panel()->standard('', $this->view->initForm());
        $this->DIC->ui()->mainTemplate()->setContent($this->DIC->ui()->renderer()->render($panel));
    }

    /**
     * @throws \ilCtrlException
     */
    public function savePermissionSettings(): void
    {
        $result = $this->model->save($this->view->initForm());
        if ($result[0]) {
            $this->DIC->ui()->mainTemplate()->setOnScreenMessage('success', $result[1], true);
            $this->DIC->ctrl()->redirectByClass(\ilLearnplacesConfigGUI::class, self::CMD_SHOW_PERMISSION_SETTINGS);
        } else {
            $this->DIC->ui()->maintemplate()->setOnScreenMessage('failure', $result[1]);
            $this->showPermissionSettings();
            return;
        }
    }
}