<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\PictureBlock;

use ilButtonToSplitButtonMenuItemAdapter;
use ilCtrl;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;
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
use SRAG\Learnplaces\service\publicapi\model\PictureBlockModel;
use SRAG\Learnplaces\util\DeleteItemModal;
use xsrlPictureBlockGUI;

use function is_null;

/**
 * Class PictureBlockPresentationView
 *
 * @package SRAG\Learnplaces\gui\block\PictureBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;
    use DeleteItemModal;

    public const SEQUENCE_ID_PREFIX = 'block_';
    public const TYPE = 'picture';

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
     * @var PictureBlockModel $model
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
        $this->template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/block/tpl.picture.html', true, true);
    }

    /**
     * @return void
     */
    private function initView(): void
    {
        $this->template->setVariable('TITLE', $this->model->getTitle());

        $resourceStorage = PluginContainer::resolve('resourceStorage');
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');

        $resourceId = $this->model->getPicture()->getResourceId();
        $resource = new ResourceIdentification($resourceId);
        if ($resourceStorage->manage()->find($resourceId)) {
            $src = $resourceStorage->consume()
                ->src($resource)
                ->getSrc();
            $pictureHTML = $renderer->render(
                $factory->image()->responsive($src, 'Block picture')
            );
            $this->template->setVariable('CONTENT', $pictureHTML);
        }

        $this->template->setVariable('DESCRIPTION', $this->model->getDescription());
    }

    /**
     * @param PictureBlockModel $model
     * @return void
     */
    public function setModel(PictureBlockModel $model): void
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

        /** @var \ILIAS\UI\Factory $factory */
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');
        $lng = PluginContainer::resolve('lng');

        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlPictureBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlPictureBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
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
        $outerTemplate->setVariable('CONTENT', $blockTemplate->get());
        return $outerTemplate;
    }
}
