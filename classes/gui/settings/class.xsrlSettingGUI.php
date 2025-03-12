<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\gui\block\util\ReferenceIdAware;
use KPG\Learnplaces\gui\exception\ValidationException;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\gui\settings\SettingEditFormView;
use KPG\Learnplaces\gui\settings\SettingModel;
use KPG\Learnplaces\service\publicapi\block\ConfigurationService;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\block\LocationService;
use KPG\Learnplaces\service\security\AccessGuard;
use KPG\Learnplaces\container\PluginContainer;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Label\Font\OpenSans;
use ILIAS\Data\ReferenceId;
use ILIAS\UI\Factory;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use Endroid\QrCode\Writer\Result\ResultInterface;
use JetBrains\PhpStorm\NoReturn;
use KPG\Learnplaces\util\QrCode;

/**
 * Class xsrlSettingGUI
 *
 * @package KPG\Learnplaces\gui\settings
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class xsrlSettingGUI
{
    use ReferenceIdAware;

    public const CMD_QR_CODE_DOWNLOAD = 'downloadQrCode';

    public const TAB_ID = 'Settings';
    public const BLOCK_ID_QUERY_KEY = 'block';

    /**
     * @var ilTabsGUI $tabs
     */
    private $tabs;
    /**
     * @var ilGlobalPageTemplate | ilTemplate $template
     */
    private $template;
    /**
     * @var ilCtrl $controlFlow
     */
    private $controlFlow;
    /**
     * @var ilLearnplacesPlugin $plugin
     */
    private $plugin;
    /**
     * @var ConfigurationService $configService
     */
    private $configService;
    /**
     * @var LocationService $locationService
     */
    private $locationService;
    /**
     * @var LearnplaceService $learnplaceService
     */
    private $learnplaceService;
    /**
     * @var ServerRequestInterface $request
     */
    private $request;
    /**
     * @var AccessGuard $accessGuard
     */
    private $accessGuard;

    /**
     * xsrlSettingGUI constructor.
     *
     * @param ilTabsGUI $tabs
     * @param ilGlobalPageTemplate | ilTemplate $template
     * @param ilCtrl $controlFlow
     * @param ilLearnplacesPlugin $plugin
     * @param ConfigurationService $configService
     * @param LocationService $locationService
     * @param LearnplaceService $learnplaceService
     * @param ServerRequestInterface $request
     * @param AccessGuard $accessGuard
     */
    public function __construct(ilTabsGUI $tabs, $template, ilCtrl $controlFlow, ilLearnplacesPlugin $plugin, ConfigurationService $configService, LocationService $locationService, LearnplaceService $learnplaceService, ServerRequestInterface $request, AccessGuard $accessGuard)
    {
        $this->tabs = $tabs;
        $this->template = $template;
        $this->controlFlow = $controlFlow;
        $this->plugin = $plugin;
        $this->configService = $configService;
        $this->locationService = $locationService;
        $this->learnplaceService = $learnplaceService;
        $this->request = $request;
        $this->accessGuard = $accessGuard;
    }

    /**
     * @return bool
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function executeCommand(): bool
    {
        $cmd = $this->controlFlow->getCmd(CommonControllerAction::CMD_INDEX);
        $this->tabs->activateTab(self::TAB_ID);

        switch ($cmd) {
            case CommonControllerAction::CMD_CANCEL:
            case CommonControllerAction::CMD_EDIT:
            case CommonControllerAction::CMD_UPDATE:
            case self::CMD_QR_CODE_DOWNLOAD:
                if ($this->accessGuard->hasWritePermission()) {
                    $this->{$cmd}();
                    if ($this->template instanceof ilGlobalPageTemplate) {
                        $this->template->printToStdout();
                    } else {
                        $this->template->show();
                    }
                    return true;
                }
                break;
        }

        $this->template->setOnScreenMessage('failure', $this->plugin->txt('common_access_denied'), true);
        $this->controlFlow->redirectByClass(ilRepositoryGUI::class);

        return false;
    }

    /**
     * @return void
     */
    private function edit(): void
    {
        $learnplce = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $config = $learnplce->getConfiguration();
        $location = $learnplce->getLocation();
        $objectId = ilObject::_lookupObjectId($this->getCurrentRefId());
        $model = new SettingModel();
        $model
            ->setLatitude($location->getLatitude())
            ->setLongitude($location->getLongitude())
            ->setRadius($location->getRadius())
            ->setElevation($location->getElevation())
            ->setOnline($config->isOnline())
            ->setDefaultVisibility($config->getDefaultVisibility())
            ->setTitle(ilObject::_lookupTitle($objectId))
            ->setDescription(ilObject::_lookupDescription($objectId))
            ->setMapZoom($config->getMapZoomLevel());

        $view = new SettingEditFormView($model, $this->plugin, $this->controlFlow);
        $view->fillForm();

        $qrCodePanel = $this->getQrCodePanel();

        $this->template->setContent($view->getHTML() . $qrCodePanel);
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function update(): void
    {

        $view = new SettingEditFormView(new SettingModel(), $this->plugin, $this->controlFlow);

        try {
            $learnplce = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
            $config = $learnplce->getConfiguration();
            $location = $learnplce->getLocation();

            $settings = $view->getSettings();
            $config
                ->setOnline($settings->isOnline())
                ->setDefaultVisibility($settings->getDefaultVisibility())
                ->setMapZoomLevel($settings->getMapZoom());
            $this->configService->store($config);

            $location
                ->setLatitude($settings->getLatitude())
                ->setLongitude($settings->getLongitude())
                ->setElevation($settings->getElevation())
                ->setRadius($settings->getRadius());
            $this->locationService->store($location);

            $pluginObject = new ilObjLearnplaces($this->getCurrentRefId());
            $pluginObject->setTitle($settings->getTitle());
            $pluginObject->setDescription($settings->getDescription());
            $pluginObject->update();

            $this->template->setOnScreenMessage('success', $this->plugin->txt('message_changes_save_success'), true);
            $this->controlFlow->redirect($this, CommonControllerAction::CMD_EDIT);
        } catch (ValidationException $ex) {
            $view->setValuesByPost();
            $this->template->setContent($view->getHTML());
        }
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function cancel(): void
    {
        $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX);
    }

    /**
     * @return string
     */
    private function getQrCodePanel(): string
    {
        /** @var Factory $f */
        $f = PluginContainer::resolve('factory');
        $r = PluginContainer::resolve('renderer');
        $ctrl = PluginContainer::resolve('ctrl');

        $obj_qr_code = new QrCode();

        $token = $obj_qr_code->createToken();

        $qrCode = $this->getQrCode($token, 'Lernort');

        $uri = $qrCode->getDataUri();

        $qrCodeImage = $r->render(
            $f->image()->standard($uri, 'QR-Code')
        );

        $buttonDownloadAction = $ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlSettingGUI::class], self::CMD_QR_CODE_DOWNLOAD);
        $downloadButton = $r->render(
            $f->button()->standard('Download', $buttonDownloadAction)
        );

        $qrCodePanel = $f->panel()->standard('QR-Code', $f->legacy(
            $downloadButton
            . "<br>"
            . $qrCodeImage
        ));

        return $r->render($qrCodePanel);
    }

    #[NoReturn]
    public function downloadQrCode(): void
    {
        $obj_qr_code = new QrCode();
        $token = $obj_qr_code->createToken();
        $qrCode = $this->getQrCode($token, 'Lernort');
        $binary = $qrCode->getString();

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="Lernort-QR-Code.png"');

        if (ob_get_level() > 0) {
            ob_clean();
        }
        flush();

        echo $binary;
        exit;
    }

    /**
     * @param string $url
     * @param string $label
     * @return ResultInterface
     */
    private function getQrCode(string $url, string $label): ResultInterface
    {
        return Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->size(300)
            ->margin(10)
            ->labelText($label)
            ->labelFont(new OpenSans(30))
            ->build();
    }
}
