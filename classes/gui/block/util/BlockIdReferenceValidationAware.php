<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\util;

use ilCtrl;
use ilLearnplacesPlugin;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\security\AccessGuard;
use xsrlContentGUI;
use ilCtrlException;

/**
 * Trait BlockIdReferenceValidationAware
 *
 * @package KPG\Learnplaces\gui\block\util
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait BlockIdReferenceValidationAware
{
    private AccessGuard $blockAccessGuard;
    private ilLearnplacesPlugin $plugin;
    private ilCtrl $controlFlow;

    /**
     * @param int $blockId
     * @return void
     * @throws ilCtrlException
     */
    private function redirectInvalidRequests(int $blockId): void
    {
        if (!$this->blockAccessGuard->isValidBlockReference($blockId)) {
            $this->template->setOnScreenMessage('failure', $this->plugin->txt('common_access_denied'), true);
            $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX);
        }
    }

}
