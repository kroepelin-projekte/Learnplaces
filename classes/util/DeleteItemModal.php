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
     * @return string
     * @throws \ilCtrlException
     */
    private function deleteItemButtonWithModal(string $item, string $item_title, string $message, string $button_label): string
    {
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');
        $query = PluginContainer::resolve('query');
        $refinery = PluginContainer::resolve('refinery');
        $ctrl = PluginContainer::resolve('ctrl');

        // receive GET variable and open modal
        if ($query->has('item') && $query->retrieve('item', $refinery->kindlyTo()->string()) === $item) {
            $item = $query->retrieve('item', $refinery->kindlyTo()->string());
            $affected_item = $factory->modal()->interruptiveItem($item, $item_title);

            $modal = $factory->modal()
                ->interruptive($button_label, $message, '#')
                ->withAffectedItems([$affected_item]);

            exit($renderer->render($modal));
        }

        $ajax_url = $ctrl->getLinkTargetByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX) . '&item=' . $item;

        $modal = $factory->modal()->interruptive('', '', '')
            ->withAsyncRenderUrl($ajax_url);

        $button = $factory->button()->standard($button_label, '#')
            ->withOnClick($modal->getShowSignal());

        return $renderer->render([
            $modal,
            $button
        ]);
    }
}
