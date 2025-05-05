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
            case self::CMD_REFRESH_SECRET:
                $this->refreshSecret();
                break;
        }
    }

    public function showAPISettings(): void
    {
        $panel = $this->DIC->ui()->factory()->panel()->standard('', $this->view->initForm());
        $panel_secret = $this->DIC->ui()->factory()->panel()->standard(
            $this->plugin->txt(self::LANG_SETTINGS), [$this->view->buildSecretInformation(), $this->view->buildRefreshButton()]
        );
        $this->DIC->ui()->mainTemplate()->setContent($this->DIC->ui()->renderer()->render([$panel, $panel_secret]));
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
        }
    }

    public function refreshSecret(): void
    {
        $this->model->refreshSecret();
        $this->DIC->ui()->mainTemplate()->setOnScreenMessage('success', $this->plugin->txt(self::LANG_SECRET_SUCCESS), true);
        $this->DIC->ctrl()->redirectByClass(\ilLearnplacesConfigGUI::class, self::CMD_SHOW_API_SETTINGS);
    }
}