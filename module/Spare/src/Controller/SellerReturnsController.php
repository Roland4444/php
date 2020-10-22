<?php

namespace Spare\Controller;

use Core\Traits\RestMethods;
use Spare\Entity\SellerReturn;
use Spare\Form\SellerReturnForm;
use Spare\Service\SellerReturnsService;
use Spare\Service\SellerService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SellerReturnsController extends AbstractActionController
{
    use RestMethods;

    private $service;
    private $sellerService;

    /**
     * SellerReturnsController constructor.
     * @param SellerReturnsService $service
     * @param SellerService $sellerService
     */
    public function __construct($service, $sellerService)
    {
        $this->service = $service;
        $this->sellerService = $sellerService;
    }

    public function indexAction()
    {
        $sellers = $this->sellerService->getTableList([]);

        return new ViewModel([
            'sellers' => json_encode($sellers),
        ]);
    }

    public function listAction()
    {
        $requestData = [
            'startDate' => $this->request->getQuery('startDate'),
            'endDate' => $this->request->getQuery('endDate'),
            'seller' => $this->request->getQuery('seller')
        ];

        $filterForm = $this->getFilterForm();
        $filterForm->setData($requestData);

        if ($filterForm->isValid()) {
            $data = $this->service->getAll($requestData);
            return $this->responseSuccess(['data' => $data]);
        } else {
            return $this->responseError("Не корректно заполнены поля фильтра.", Response::STATUS_CODE_400);
        }
    }

    public function deleteAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id', null);
        try {
            $this->service->remove($id);
            return $this->responseSuccess();
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            $postData = json_decode($this->getRequest()->getContent());

            if ($postData->id) {
                $sellerReturn = $this->service->find($postData->id);
            } else {
                $sellerReturn = new SellerReturn();
            }

            $form = new Form();
            $form->setInputFilter($sellerReturn->getInputFilter());
            $form->setData((array)$postData);

            if ($form->isValid()) {
                $result = $this->service->store($postData, $sellerReturn);

                return $this->responseSuccess([
                    'seller' => $result
                ]);
            } else {
                return $this->responseError("Не корректно заполнены поля. ", Response::STATUS_CODE_400);
            }
        }

        return $this->responseError('illegal action', 405);
    }

    private function getFilterForm()
    {
        return new SellerReturnForm();
    }
}
