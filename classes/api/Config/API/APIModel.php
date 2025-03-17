<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Config\API;

use ILIAS\DI\Container;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\constConfig;

class APIModel implements constConfig
{
    private \ilLearnplacesPlugin $plugin;
    private Container $DIC;

    public function __construct(\ilLearnplacesPlugin $plugin, Container $DIC)
    {
        $this->plugin = $plugin;
        $this->DIC = $DIC;
    }

    public function save($form): array
    {
        $form = $form->withRequest($this->DIC->http()->request());
        $result = $form->getData();
        if ($result === null) {
            return [false, $this->plugin->txt(self::LANG_ERROR_REQUIRED_FIELD)];
        }
        Settings::setBaseUrl($result['api']['url']);
        Settings::setCookieExpire($result['api']['cookie']);
        Settings::setClientURL($result['api']['client']);
        Settings::setSecret($result['api']['secret']);

        return [true, $this->plugin->txt(self::LANG_SUCCESS_SETTINGS)];
    }
}