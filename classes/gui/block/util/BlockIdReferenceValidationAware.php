<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\util;

use ilCtrl;
use ilLearnplacesPlugin;
use ilUtil;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\security\AccessGuard;
use xsrlContentGUI;

/**
 * Trait BlockIdReferenceValidationAware
 *
 * @package SRAG\Learnplaces\gui\block\util
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait BlockIdReferenceValidationAware
{
    /**
     * @var AccessGuard $blockAccessGuard
     */
    private $blockAccessGuard;
    /**
     * @var ilLearnplacesPlugin $plugin
     */
    private $plugin;
    /**
     * @var ilCtrl $controlFlow
     */
    private $controlFlow;

    /**
     * @param int $blockId
     * @return void
     * @throws \ilCtrlException
     */
    private function redirectInvalidRequests(int $blockId): void
    {
        if(!$this->blockAccessGuard->isValidBlockReference($blockId)) {
            $this->template->setOnScreenMessage('failure', $this->plugin->txt('common_access_denied'), true);
            $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX);
        }
    }

}
