<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\util;

use Closure;
use ilObjPluginDispatchGUI;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use xsrlAccordionBlockGUI;
use xsrlContentGUI;

trait DeleteItemModal
{
    /**
     * @param string $item
     * @param string $item_title
     * @param string $message
     * @param string $button_label
     * @return array
     * @throws \ilCtrlException
     */
    private function deleteItemButtonWithModal(string $item, string $item_title, string $message, string $button_label): array
    {
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');
        $query = PluginContainer::resolve('query');
        $refinery = PluginContainer::resolve('refinery');
        $ctrl = PluginContainer::resolve('ctrl');

        if (version_compare(ILIAS_VERSION_NUMERIC, '9.0', '>=')) {
            $affected_item = $factory->modal()->interruptiveItem()
                ->standard($item, $item_title);
        } else {
            $affected_item = $factory->modal()->interruptiveItem($item, $item_title);
        }

        $modal = $factory->modal()
            ->interruptive(
                $button_label,
                $message,
                $this->controlFlow->getLinkTargetByClass(xsrlContentGUI::class, CommonControllerAction::CMD_DELETE) . '&item=' . $item
            )
            ->withAffectedItems([$affected_item]);

        $button = $factory->button()->shy($button_label, '#')
            ->withOnClick($modal->getShowSignal());

        return [
            'modal' => $renderer->render($modal),
            'button' => $button,
        ];
    }
}
