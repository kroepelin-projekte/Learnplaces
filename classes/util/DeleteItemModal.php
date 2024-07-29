<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\util;

use Closure;
use ilObjPluginDispatchGUI;
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
        global $DIC;

        $f = $DIC->ui()->factory();
        $r = $DIC->ui()->renderer();
        $superglobal = $DIC->http()->wrapper();
        $refinery = $DIC->refinery();

        // receive GET variable and open modal
        if ($superglobal->query()->has('item') && $superglobal->query()->retrieve('item', $refinery->kindlyTo()->string()) === $item) {
            $item = $superglobal->query()->retrieve('item', $DIC->refinery()->kindlyTo()->string());
            $affected_item = $f->modal()->interruptiveItem($item, $item_title);

            $modal = $f->modal()
                ->interruptive($button_label, $message, '#')
                ->withAffectedItems([$affected_item]);

            exit($r->render($modal));
        }

        $ajax_url = $DIC->ctrl()->getLinkTargetByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX) . '&item=' . $item;

        $modal = $f->modal()->interruptive('', '', '')
            ->withAsyncRenderUrl($ajax_url);

        $button = $f->button()->standard($button_label, '#')
            ->withOnClick($modal->getShowSignal());

        return $r->render([
            $modal,
            $button
        ]);
    }
}
