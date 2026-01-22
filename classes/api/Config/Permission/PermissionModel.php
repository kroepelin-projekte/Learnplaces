<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Config\Permission;

use ILIAS\DI\Container;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\constConfig;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use ILIAS\UI\Component\Input\Container\Form\Standard;

class PermissionModel implements constConfig
{
    private \ilLearnplacesPlugin $plugin;
    private Container $DIC;

    public function __construct(\ilLearnplacesPlugin $plugin, Container $DIC)
    {
        $this->plugin = $plugin;
        $this->DIC = $DIC;
    }

    public function save(Standard $initForm): array
    {
        $form = $initForm->withRequest($this->DIC->http()->request());
        $result = $form->getData();
        if ($result === null) {
            return [false, $this->plugin->txt(self::LANG_ERROR_REQUIRED_FIELD)];
        }
        Settings::setPermissionRoles($result['permission']['roles']);
        return [true, $this->plugin->txt(self::LANG_SUCCESS_SETTINGS)];
    }
}
