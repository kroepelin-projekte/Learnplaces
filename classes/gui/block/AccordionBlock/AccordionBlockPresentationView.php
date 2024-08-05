<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\AccordionBlock;

use ilButtonToSplitButtonMenuItemAdapter;
use ilCtrl;
use ILIAS\UI\Implementation\Component\Input\Field\Section;
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
use SRAG\Learnplaces\gui\ContentPresentationView;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use SRAG\Learnplaces\util\DeleteItemModal;
use xsrlAccordionBlockGUI;

/**
 * Class AccordionBlockPresentationView
 *
 * @package SRAG\Learnplaces\gui\block\AccordionBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class AccordionBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;
    use DeleteItemModal;

    public const SEQUENCE_ID_PREFIX = 'block_';
    public const TYPE = 'accordion';

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
     * @var AccordionBlockModel $model
     */
    private $model;
    /**
     * @var ContentPresentationView $contentView
     */
    private $contentView;

    /**
     * PictureUploadBlockPresentationView constructor.
     *
     * @param ilLearnplacesPlugin     $plugin
     * @param ilCtrl                  $controlFlow
     * @param ContentPresentationView $contentView
     */
    public function __construct(ilLearnplacesPlugin $plugin, ilCtrl $controlFlow, ContentPresentationView $contentView)
    {
        $this->plugin = $plugin;
        $this->controlFlow = $controlFlow;
        $this->contentView = $contentView;
        $this->template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/block/tpl.accordion.html', true, true);
    }

    /**
     * @return void
     */
    private function initView(): void
    {
        $this->contentView->setBlocks($this->model->getBlocks());
        $this->contentView->setAccordionId($this->model->getId());
        $this->contentView->setReadonly($this->isReadonly());

        $this->template->setVariable('ACCORDION_ID', $this->model->getId());
        $this->template->setVariable('TITLE', $this->model->getTitle());
        $this->template->setVariable('CONTENT', $this->contentView->getHTML());
        $this->template->setVariable('EXPANDED', $this->model->isExpand() ? 'in' : '');
    }

    /**
     * @param AccordionBlockModel $model
     * @return void
     */
    public function setModel(AccordionBlockModel $model): void
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getHtml(): string
    {
        if(is_null($this->model)) {
            throw new LogicException('The accordion block view requires a model to render its content.');
        }

        $this->initView();
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
        $lng = PluginContainer::resolve('lng');

        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlAccordionBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlAccordionBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->shy($this->plugin->txt('common_edit'), $editAction);

        $deleteButton = $this->deleteItemButtonWithModal(
            $this->model->getId() . '-' . self::TYPE,
            $this->model->getTitle(),
            $this->plugin->txt('confirm_delete_header'),
            $this->plugin->txt('common_delete')
        );

        $actionMenu = $renderer->render($factory->dropdown()->standard([
            $editButton,
            $deleteButton['button']
        ])->withLabel($lng->txt('actions')));

        //fill outer template
        if(!$this->isReadonly()) {
            $outerTemplate->setVariable('ACTION_BUTTON', $actionMenu . $deleteButton['modal']);
        }
        $outerTemplate->setVariable('CONTENT', $template->get());
        $outerTemplate->setVariable('SEQUENCE', $this->model->getSequence());
        return $outerTemplate;
    }
}
