<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\MapBlock;

use ilCtrl;
use ILIAS\HTTP\Services;
use ilLearnplacesPlugin;
use ilLinkButton;
use ilMapUtil;
use ilObject;
use ilTemplate;
use ilToolbarGUI;
use LogicException;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\model\ConfigurationModel;
use SRAG\Learnplaces\service\publicapi\model\LocationModel;
use SRAG\Learnplaces\service\publicapi\model\MapBlockModel;
use SRAG\Learnplaces\service\publicapi\model\PictureBlockModel;
use SRAG\Learnplaces\util\DeleteItemModal;
use xsrlMapBlockGUI;

/**
 * Class MapBlockPresentationView
 *
 * @package SRAG\Learnplaces\gui\block\MapBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class MapBlockPresentationView
{
    use ReadOnlyViewAware;
    use DeleteItemModal;

    public const TYPE = 'map';

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
     * @var LocationModel $location
     */
    private $location;
    /**
     * @var ConfigurationModel $configuration
     */
    private $configuration;
    /** @var Services $http */
    private object $http;
    private object $refinery;


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
        $this->http = PluginContainer::resolve('http');
        $this->refinery = PluginContainer::resolve('refinery');
        $this->template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/tpl.map_tab.html', true, true);
    }

    private function initView()
    {
        //setup button
        global $DIC;
        $factory = $DIC->ui()->factory();
        $renderer = $DIC->ui()->renderer();

        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlMapBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlMapBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->standard($this->plugin->txt('common_edit'), $editAction);

        $affected_item = $factory->modal()->interruptiveItem('deleteMap', 'Map');
        $modal = $factory->modal()->interruptive(
            $this->plugin->txt('common_delete'),
            $this->plugin->txt('confirm_delete_header'),
            $this->controlFlow->getLinkTargetByClass(xsrlMapBlockGUI::class, CommonControllerAction::CMD_DELETE) . '&block=' . $this->model->getId()
        )
            ->withAffectedItems([$affected_item]);
        $deleteButton = $factory->button()->standard($this->plugin->txt('common_delete'), '')
            ->withOnClick($modal->getShowSignal());

        $toolbar = $DIC->toolbar();
        $toolbar->addComponent($editButton);
        $toolbar->addComponent($deleteButton);
        $toolbar->addComponent($modal);

        $map = ilMapUtil::getMapGUI();
        $map->setMapId($map_id = "map_" . hash('sha256', uniqid('map', true)))
                ->setLatitude((string) $this->location->getLatitude())
                ->setLongitude((string) $this->location->getLongitude())
                ->setZoom($this->configuration->getMapZoomLevel())
                ->setEnableTypeControl(true)
                ->setEnableLargeMapControl(true)
                ->setEnableUpdateListener(false)
                ->setEnableCentralMarker(true)
                ->setWidth('100%')
                ->setHeight('500px');

        $this->template->setVariable('CONTENT', $map->getHtml());
    }

    public function setModels(MapBlockModel $model, LocationModel $location, ConfigurationModel $configuration)
    {
        $this->model = $model;
        $this->location = $location;
        $this->configuration = $configuration;
    }


    /**
     * @inheritDoc
     */
    public function getHtml(): string
    {
        if(is_null($this->model)) {
            throw new LogicException('The picture block view requires a model to render its content.');
        }

        $this->initView();
        return $this->template->get();
    }

}
