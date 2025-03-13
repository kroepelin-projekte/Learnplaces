<?php

namespace Repository\RepositoryObject\Learnplaces\classes\api\Config\Permission;

use Repository\RepositoryObject\Learnplaces\classes\api\Config\constConfig;
use ILIAS\DI\Container;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use ILIAS\UI\Component\Input\Container\Form\Standard;

class PermissionView implements constConfig
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
    public function initForm(): Standard
    {
        $form_action = $this->DIC->ctrl()->getLinkTargetByClass(
            \ilLearnplacesConfigGUI::class, self::CMD_SAVE_PERMISSION_SETTINGS
        );

        $roles_input = $this->DIC->ui()->factory()->input()->field()->text(
            $this->plugin->txt(self::LANG_INPUT_TEXT_ROLES),
            $this->plugin->txt(self::LANG_INPUT_TEXT_ROLES_BYLINE)
        )->withValue(Settings::getPermissionRoles())->withRequired(true);

        $sections = $this->DIC->ui()->factory()->input()->field()->section(
            ['roles' => $roles_input], $this->plugin->txt(self::LANG_SETTINGS)
        );
        return $this->DIC->ui()->factory()->input()->container()->form()->standard($form_action, ['permission' => $sections]);
    }
}