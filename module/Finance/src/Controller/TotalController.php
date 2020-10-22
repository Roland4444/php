<?php

namespace Finance\Controller;

use Core\Traits\RestMethods;
use Finance\Service\TotalService;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class TotalController extends AbstractActionController
{
    use RestMethods;

    protected bool $withoutCash = false;
    protected string $indexRoute = 'mainTotal';

    protected TotalService $financeTotalService;

    /**
     * TotalController constructor.
     * @param $financeTotalService
     */
    public function __construct($financeTotalService)
    {
        $this->financeTotalService = $financeTotalService;
    }

    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'apiUrl' => $this->indexRoute
        ]);
    }

    public function jsonAction(): Response
    {
        $dateFrom = $this->getRequest()->getPost('startdate');
        $dateTo = $this->getRequest()->getPost('enddate');

        $json = [
            'rows' => [
                'total' => $this->financeTotalService->getExpenses($dateFrom, $dateTo),
                'accounts' => $this->financeTotalService->getBalance($dateTo, $this->withoutCash),
                'parents' => $this->financeTotalService->getTraderBalance($dateTo),
                'factoring' => $this->factoringTotal($dateTo)
            ],
        ];
        return $this->responseSuccess($json);
    }
}
