<?php

namespace ShipmentDocs\Controller;

use Core\Traits\RestMethods;
use ShipmentDocs\Exception\ServiceException;
use ShipmentDocs\Service\ApiService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\View\Model\ViewModel;

/** @method FlashMessenger flashMessenger() */
abstract class AbstractCrudController extends AbstractActionController
{
    use RestMethods;

    protected string $indexRoute;
    protected ApiService $service;

    /**
     * @param ApiService $service
     */
    public function __construct(ApiService $service)
    {
        $this->service = $service;
    }

    public function indexAction(): ViewModel
    {
        try {
            $response = $this->service->apiAction('GET', $this->url);
            $rows = json_decode($response, true);
            return new ViewModel([
                'data' => $rows,
                'permissions' => $this->getPermissions()
            ]);
        } catch (ServiceException $e) {
            return new ViewModel(['error' => 'Произошла ошибка: ' . $e->getMessage()]);
        }
    }

    public function addAction(): ViewModel
    {
        return new ViewModel();
    }

    public function editAction(): ViewModel
    {
        $id = $this->params()->fromRoute('id');
        try {
            $response = $this->service->apiAction('GET', $this->url . '/' . $id);
            return new ViewModel(['entity' => $response]);
        } catch (ServiceException $e) {
            return new ViewModel(['error' => 'При выполнении запроса произошла ошибка ' . $e->getMessage()]);
        }
    }

    public function saveAction(): Response
    {
        try {
            $content = $this->getRequest()->getContent();
            $data = json_decode($content, true);
            $response = $this->service->apiAction('POST', $this->url, ['json' => $data]);
            return $this->responseSuccess(['message' => 'сохранение выполнено', 'data' => $response ]);
        } catch (ServiceException $e) {
            return $this->responseError('При выполнении запроса произошла ошибка ' . $e->getMessage());
        }
    }

    public function deleteAction(): Response
    {
        try {
            $id = $this->params()->fromRoute('id');
            $this->service->apiAction('DELETE', $this->url . '/' . $id);
            $msg = 'Позиция успешно удалена';
        } catch (ServiceException $e) {
            $msg = 'При выполнении запроса произошла ошибка ' . $e->getMessage();
        }
        $this->flashMessenger()->addMessage($msg);
        return $this->redirect()->toRoute($this->indexRoute);
    }

    protected function getPermissions(): array
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'delete' => $this->hasAccess(static::class, 'delete'),
            'edit' => $this->hasAccess(static::class, 'delete')
        ];
    }

    protected function handleRestResponse(callable $callback)
    {
        try {
            $response = $callback();
            return $this->responseSuccess(['data' => $response]);
        } catch (ServiceException $e) {
            return $this->responseError('При выполнении запроса произошла ошибка: ' . $e->getMessage());
        }
    }
}
