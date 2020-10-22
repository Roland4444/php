<?php

namespace Modules\Controller;

use Application\Form\Filter\FilterableController;
use Core\Controller\CrudController;
use Dompdf\Dompdf;
use Modules\Entity\Waybill;
use Modules\Form\WaybillsForm;
use Modules\Service\WaybillSettingsService;
use Modules\Service\WaybillsFill;
use Application\Form\Filter\VehicleElement;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\SubmitElement;
use Modules\Service\WaybillsService;

/**
 * Class WaybillsController
 * @package Modules\Controller
 */
class WaybillsController extends CrudController
{
    use FilterableController;

    /**
     * @var string
     */
    protected string $indexRoute = 'waybills';

    /**
     * @var \Modules\Service\WaybillsService
     */
    protected $service;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $container;

    /**
     * Создание по заданнанным параметрам путевого листа и экспорт его в pdf
     *
     */
    public function createAction()
    {
        $filling = new WaybillsFill($this->params()->fromRoute('id'), $this->services);
        try {
            $html = $filling->getRenderHtml();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }

        //$options = new Options(['defaultFont' => 'DejaVu Sans']);

        $domPDF = new Dompdf();
        //$domPDF->setOptions($options);
        $domPDF->setPaper('A4', 'landscape');
        $domPDF->loadHtml($html, 'UTF-8');
        $domPDF->render();
        $domPDF->stream("document.pdf", ["Attachment" => 0]);
        exit();
    }

    /**
     * Получить данные для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    protected function getTableListData($params)
    {
        $orderBy = [
            'waybillNumber' => 'DESC',
            'dateStart' => 'DESC'
        ];
        return $this->service->findBy($params, $orderBy);
    }

    /**
     * Возвращает фильтр для indexAction
     *
     * @param array
     * @return
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new VehicleElement(new DateElement($this->entityManager)));
    }

    /**
     * Возвращает форму для создания
     *
     * @return mixed
     */
    protected function getCreateForm()
    {
        $form = new WaybillsForm($this->entityManager);
        $nextWaybillNumber = $this->services[WaybillsService::class]->getNextWaybillNumber();
        $waybill = new Waybill();
        $defaultSettings = $this->services[WaybillSettingsService::class]->getAllSettings();
        $waybill->setDefaultParams($defaultSettings);
        $waybill->setWaybillNumber($nextWaybillNumber);
        $form->bind($waybill);
        return $form;
    }

    /**
     * Возвращает форму для редактирования
     *
     * @return mixed
     */
    protected function getEditForm()
    {
        return new WaybillsForm($this->entityManager);
    }

    /**
     * Get access to see components
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }

    /**
     * Получить entity для add action
     *
     * @param array $data
     * @return Waybill
     */
    protected function getEntityForCreate(array $data)
    {
        return new Waybill();
    }
}
