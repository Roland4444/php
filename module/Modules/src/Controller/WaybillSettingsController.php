<?php

namespace Modules\Controller;

use Modules\Entity\WaybillSettings;
use Modules\Service\WaybillSettingsService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class WaybillSettingsController extends AbstractActionController
{
    /**
     * @var WaybillSettingsService
     */
    protected $waybillSettingsService;

    /**
     * WaybillSettingsController constructor.
     * @param \Zend\ServiceManager\ServiceManager $container
     */
    public function __construct($container)
    {
        $this->waybillSettingsService = $container->get(WaybillSettingsService::class);
    }

    public function indexAction()
    {
        $message = '';
        if ($this->getRequest()->isPost()) {
            try {
                $params = array_filter($this->getRequest()->getPost()->toArray(), function ($value, $name) {
                    return in_array($name, WaybillSettings::SETTINGS);
                }, ARRAY_FILTER_USE_BOTH);

                if ($this->waybillSettingsService->updateSettings($params)) {
                    return $this->redirect()->toRoute('waybills');
                }
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
        }

        $settings = $this->waybillSettingsService->getAllSettings();

        return new ViewModel([
            'message' => $message,
            'settings' => $settings,
        ]);
    }
}
