<?php

namespace Modules\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Application\Form\Filter\VehicleElement;
use Modules\Service\WaybillsService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class WaybillsReportController extends AbstractActionController
{
    use FilterableController;

    /**
     * @var WaybillsService
     */
    protected $waybillsService;

    /**
     * WaybillsReportController constructor.
     * @param \Zend\ServiceManager\ServiceManager $container
     */
    public function __construct($container)
    {
        $this->entityManager = $container->get('entityManager');
        $this->waybillsService = $container->get(WaybillsService::class);
    }

    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), 'waybillsReport');
        $params = $filterForm->getFilterParams('waybillsReport');

        $waybills = $this->getTableListData($params);

        return new ViewModel([
            'waybills' => $waybills,
            'form' => $filterForm->getForm($params),
        ]);
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new VehicleElement(new DateElement($this->entityManager)));
    }

    /**
     * Получить данные для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    protected function getTableListData($params)
    {
        return $this->waybillsService->findBy($params);
    }
}
