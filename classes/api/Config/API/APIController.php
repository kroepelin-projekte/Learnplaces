<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Config\API;

use Repository\RepositoryObject\Learnplaces\classes\api\Config\constConfig;
use ILIAS\DI\Container;

class APIController implements constConfig
{
    private \ilLearnplacesPlugin $plugin;
    private Container $DIC;
    private APIModel $model;
    private APIView $view;

    public function __construct(\ilLearnplacesPlugin $plugin, Container $DIC)
    {
        $this->plugin = $plugin;
        $this->DIC = $DIC;
        $this->model = new APIModel($this->plugin, $this->DIC);
        $this->view = new APIView($this->plugin, $this->DIC);
    }

    /**
     * @throws \ilCtrlException
     */
    public function performCMD(string $cmd): void
    {
        switch ($cmd) {
            case self::CMD_SHOW_API_SETTINGS:
            case "configure":
                $this->showAPISettings();
                break;
            case self::CMD_SAVE_API_SETTINGS:
                $this->saveAPISettings();
                break;
        }
    }

    public function showAPISettings(): void
    {
        $panel = $this->DIC->ui()->factory()->panel()->standard('', $this->view->initForm());
        $this->DIC->ui()->mainTemplate()->setContent($this->DIC->ui()->renderer()->render($panel));
    }

    /**
     * @throws \ilCtrlException
     */
    public function saveAPISettings(): void
    {
        $result = $this->model->save($this->view->initForm());
        if ($result[0]) {
            $this->DIC->ui()->mainTemplate()->setOnScreenMessage('success', $result[1], true);
            $this->DIC->ctrl()->redirectByClass(\ilLearnplacesConfigGUI::class, self::CMD_SHOW_API_SETTINGS);
        } else {
            $this->DIC->ui()->maintemplate()->setOnScreenMessage('failure', $result[1]);
            $this->showAPISettings();
            return;
        }
    }
}