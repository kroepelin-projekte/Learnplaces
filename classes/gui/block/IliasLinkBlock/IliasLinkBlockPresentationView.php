<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\IliasLinkBlock;

use ilButtonToSplitButtonMenuItemAdapter;
use ilCtrl;
use ilLearnplacesPlugin;
use ilLink;
use ilLinkButton;
use ilObject;
use ilSplitButtonException;
use ilSplitButtonGUI;
use ilTemplate;
use ilTextInputGUI;
use LogicException;
use SRAG\Learnplaces\gui\block\Renderable;
use SRAG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use SRAG\Learnplaces\util\DeleteItemModal;
use xsrlIliasLinkBlockGUI;
use xsrlPictureBlockGUI;

/**
 * Class IliasLinkBlockPresentationView
 *
 * @package SRAG\Learnplaces\gui\block\IliasLinkBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class IliasLinkBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;
    use DeleteItemModal;


    public const SEQUENCE_ID_PREFIX = 'block_';
    public const TYPE = 'link';

    /**
     * @var ilLearnplacesPlugin $plugin
     */
    private $plugin;
    /**
     * @var ilTemplate $template
     */
    private $template;
    /**
     * @var ilCtrl $controlFlow
     */
    private $controlFlow;
    /**
     * @var ILIASLinkBlockModel $model
     */
    private $model;


    /**
     * PictureUploadBlockPresentationView constructor.
     *
     * @param ilLearnplacesPlugin $plugin
     * @param ilCtrl              $controlFlow
     */
    public function __construct(ilLearnplacesPlugin $plugin, ilCtrl $controlFlow)
    {
        $this->plugin = $plugin;
        $this->controlFlow = $controlFlow;
        $this->template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/block/tpl.ilias_link.html', true, true);
    }

    private function initView(): void
    {

        $objectId = ilObject::_lookupObjectId($this->model->getRefId());

        $this->template->setVariable('CONTENT', ilLink::_getStaticLink($this->model->getRefId()));
        $this->template->setVariable('TITLE', ilObject::_lookupTitle($objectId));
        $this->template->setVariable('DESCRIPTION', ilObject::_lookupDescription($objectId));
    }

    public function setModel(ILIASLinkBlockModel $model): void
    {
        $this->model = $model;
        $this->initView();
    }


    /**
     * @inheritDoc
     */
    public function getHtml(): string
    {
        if(is_null($this->model)) {
            throw new LogicException('The picture block view requires a model to render its content.');
        }

        return $this->wrapWithBlockTemplate($this->template)->get();
    }

    /**
     * Wraps the given template with the tpl.block.html template.
     *
     * @param ilTemplate $blockTemplate The block template which should be wrapped.
     * @return ilTemplate               The wrapped template.
     *
     * @throws ilSplitButtonException   Thrown if something went wrong with the split button.
     */
    private function wrapWithBlockTemplate(ilTemplate $blockTemplate): ilTemplate
    {
        $outerTemplate = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/tpl.block.html', true, true);

        // todo
        global $DIC;
        $factory = $DIC->ui()->factory();
        $renderer = $DIC->ui()->renderer();

        //setup button
        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlIliasLinkBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlIliasLinkBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->standard($this->plugin->txt('common_edit'), $editAction);
        $htmlEditButton = $renderer->render($editButton);

        $deleteButton = $this->deleteItemButtonWithModal(
            $this->model->getId() . '-' . self::TYPE,
            ilObject::_lookupTitle(ilObject::_lookupObjId($this->model->getRefId())),
            $this->plugin->txt('confirm_delete_header'),
            $this->plugin->txt('common_delete')
        );

        //setup sequence number
        $input = new ilTextInputGUI('', self::SEQUENCE_ID_PREFIX . $this->model->getId());
        $input->setRequired(true);
        $input->setValidationRegexp('/^\d+$/');
        $input->setValue($this->model->getSequence());
        $input->setRequired(true);

        //fill outer template
        if(!$this->isReadonly()) {
            $outerTemplate->setVariable('ACTION_BUTTON', $htmlEditButton . $deleteButton);
            $outerTemplate->setVariable('SEQUENCE_INPUT', $input->render());
        }
        $outerTemplate->setVariable('CONTENT', $blockTemplate->get());
        $outerTemplate->setVariable('SEQUENCE', $this->model->getSequence());
        return $outerTemplate;
    }
}
