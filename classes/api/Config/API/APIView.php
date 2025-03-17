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

        $url_input = $this->DIC->ui()->factory()->input()->field()->text(
            $this->plugin->txt(self::LANG_INPUT_TEXT_BASE_URL),
            $this->plugin->txt(self::LANG_INPUT_TEXT_BASE_URL_BYLINE)
        )->withValue(Settings::getBaseURL())->withRequired(true);
        $cookie_input = $this->DIC->ui()->factory()->input()->field()->numeric(
            $this->plugin->txt(self::LANG_INPUT_TEXT_COOKIE_EXPIRE),
            $this->plugin->txt(self::LANG_INPUT_TEXT_COOKIE_EXPIRE_BYLINE)
        )->withValue(Settings::getCookieExpire())->withRequired(true);
        $client_input = $this->DIC->ui()->factory()->input()->field()->text(
            $this->plugin->txt(self::LANG_INPUT_TEXT_CLIENT_URL),
            $this->plugin->txt(self::LANG_INPUT_TEXT_BASE_CLIENT_BYLINE)
        )->withValue(Settings::getClientURL())->withRequired(false);
        $secret_input = $this->DIC->ui()->factory()->input()->field()->text(
            $this->plugin->txt(self::LANG_INPUT_TEXT_SECRET),
            $this->plugin->txt(self::LANG_INPUT_TEXT_SECRET_BYLINE) . ': ' . bin2hex(random_bytes(32))
        )->withValue(Settings::getSecret())->withRequired(false);
        $sections = $this->DIC->ui()->factory()->input()->field()->section(
            ['url' => $url_input, 'cookie' => $cookie_input, "client" => $client_input, "secret" => $secret_input], $this->plugin->txt(self::LANG_SETTINGS)
        );
        return $this->DIC->ui()->factory()->input()->container()->form()->standard($form_action, ['api' => $sections]);
    }
}