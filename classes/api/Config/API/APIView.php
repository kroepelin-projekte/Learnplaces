<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Config\API;

use ILIAS\DI\Container;
use ILIAS\UI;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\constConfig;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;

class APIView implements constConfig
{
    private \ilLearnplacesPlugin $plugin;
    private Container $DIC;

    public function __construct(\ilLearnplacesPlugin $plugin, Container $DIC)
    {
        $this->plugin = $plugin;
        $this->DIC = $DIC;
    }

    /**
     * @throws \ilCtrlException
     */
    public function initForm(): UI\Component\Input\Container\Form\Standard
    {
        $form_action = $this->DIC->ctrl()->getLinkTargetByClass(
            \ilLearnplacesConfigGUI::class, self::CMD_SAVE_API_SETTINGS
        );

        $cookie_input = $this->DIC->ui()->factory()->input()->field()->numeric(
            $this->plugin->txt(self::LANG_INPUT_TEXT_COOKIE_EXPIRE),
            $this->plugin->txt(self::LANG_INPUT_TEXT_COOKIE_EXPIRE_BYLINE)
        )->withValue(Settings::getCookieExpire())->withRequired(true);
        $client_input = $this->DIC->ui()->factory()->input()->field()->text(
            $this->plugin->txt(self::LANG_INPUT_TEXT_CLIENT_URL),
            $this->plugin->txt(self::LANG_INPUT_TEXT_BASE_CLIENT_BYLINE)
        )->withValue(Settings::getClientURL())->withRequired(false);
        $sections = $this->DIC->ui()->factory()->input()->field()->section(
            ['cookie' => $cookie_input, "client" => $client_input],
            $this->plugin->txt(self::LANG_SETTINGS)
        );
        return $this->DIC->ui()->factory()->input()->container()->form()->standard($form_action, ['api' => $sections]);
    }

    public function buildSecretInformation(): UI\Component\Item\Item
    {
        return $this->DIC->ui()->factory()->item()->standard($this->plugin->txt(self::LANG_SECRET_INFO));
    }
    public function buildRefreshButton(): UI\Component\Button\Button {
        $action = $this->DIC->ctrl()->getLinkTargetByClass(
            \ilLearnplacesConfigGUI::class, self::CMD_REFRESH_SECRET
        );
        return $this->DIC->ui()->factory()->button()->standard($this->plugin->txt(self::LANG_SECRET_BUTTON), $action);
    }
}