<?php

namespace ShipmentDocs\Controller;

use ShipmentDocs\Exception\ServiceException;
use ShipmentDocs\Service\ApiService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RequisitesController extends AbstractActionController
{
    protected ApiService $service;
    protected string $url = 'requisites';

    public function __construct(ApiService $service)
    {
        $this->service = $service;
    }

    public function indexAction(): ViewModel
    {
        $postData = $this->getRequest()->getPost();
        try {
            if ($this->getRequest()->isPost()) {
                $data = $this->service->apiAction('POST', $this->url, ['json' => $postData->toArray()]);
            } else {
                $data = $this->service->apiAction('GET', $this->url);
            }
            $rows = json_decode($data, true);
            $viewData = ['data' => $rows];
        } catch (ServiceException $e) {
            $viewData = [
                'errors' => ['Произошла ошибка: ' . $e->getMessage()],
                'data' => $postData->toArray()
            ];
        }
        return new ViewModel($viewData);
    }
}
