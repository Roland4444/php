<?php

namespace Factoring\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Factoring\Service\SalesService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SalesController extends AbstractActionController
{
    use FilterableController;

    private $service;

    public function __construct(SalesService $service)
    {
        $this->service = $service;
    }

    /**
     * Index action
     * @return ViewModel
     */
    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), 'factoring_sales');
        $params = $filterForm->getFilterParams('factoring_sales');

        $data = $this->getTableListData($params);

        return new ViewModel([
            'form' => $filterForm->getForm($params),
            'data' => $data,
        ]);
    }

    /**
     * Получить данные для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    protected function getTableListData($params)
    {
        $items = $this->service->findByPeriod($params['startdate'], $params['enddate']);
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item['sum'];
        }
        return [
            'items' => $items,
            'sum' => $sum,
        ];
    }

    /**
     * Filter form
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new DateElement());
    }
}
