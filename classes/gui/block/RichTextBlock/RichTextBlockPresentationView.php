<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\RichTextBlock;

use ilButtonToSplitButtonMenuItemAdapter;
use ilCtrl;
use ilLearnplacesPlugin;
use ilLinkButton;
use ilSplitButtonException;
use ilSplitButtonGUI;
use ilTemplate;
use ilTextInputGUI;
use LogicException;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\Renderable;
use SRAG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use SRAG\Learnplaces\util\DeleteItemModal;
use xsrlPictureBlockGUI;
use xsrlRichTextBlockGUI;

/**
 * Class RichTextBlockPresentationView
 *
 * @package SRAG\Learnplaces\gui\block\RichTextBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class RichTextBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;
    use DeleteItemModal;


    public const SEQUENCE_ID_PREFIX = 'block_';
    public const TYPE = 'richtext';

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
     * @var RichTextBlockModel $model
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
        $this->template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/block/tpl.rich_text.html', true, true);
    }

    /**
     * @return void
     */
    private function initView(): void
    {
        $this->template->setVariable('CONTENT', $this->model->getContent());
    }

    /**
     * @param RichTextBlockModel $model
     * @return void
     */
    public function setModel(RichTextBlockModel $model): void
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
            throw new LogicException('The rich text block view requires a model to render its content.');
        }

        return $this->wrapWithBlockTemplate($this->template)->get();
    }

    /**
     * Wraps the given template with the tpl.block.html template.
     *
     * @param ilTemplate $template      The block template which should be wrapped.
     *
     * @return ilTemplate               The wrapped template.
     *
     * @throws ilSplitButtonException   Thrown if something went wrong with the split button.
     */
    private function wrapWithBlockTemplate(ilTemplate $template): ilTemplate
    {
        $outerTemplate = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/tpl.block.html', true, true);

        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');

        //setup button
        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlRichTextBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlRichTextBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->standard($this->plugin->txt('common_edit'), $editAction);
        $htmlEditButton = $renderer->render($editButton);

        $deleteButton = $this->deleteItemButtonWithModal(
            $this->model->getId() . '-' . self::TYPE,
            '',
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
        $outerTemplate->setVariable('CONTENT', $template->get());
        $outerTemplate->setVariable('SEQUENCE', $this->model->getSequence());
        return $outerTemplate;
    }
}
