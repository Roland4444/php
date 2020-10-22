<?php

namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Core\Traits\RestMethods;
use Exception;
use Reference\Service\CustomerService;
use Reference\Service\DepartmentService;
use Storage\Entity\Weighing;
use Storage\Entity\WeighingItem;
use Storage\Plugin\CurrentDepartment;
use Storage\Service\WeighingService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class WeighingController
 * @package Storage\Controller
 * @method CurrentDepartment currentDepartment()
 * @method CurrentUser currentUser()
 */
class WeighingController extends AbstractActionController
{
    use RestMethods;
    use FilterableController;

    private const SAVE_ACTION = 'save';
    private const UPDATE_ACTION = 'update';
    private const MESSAGE = 'message';
    private const START_DATE = 'startdate';
    private const END_DATE = 'enddate';


    private string $weighingDir;
    private WeighingService $service;

    protected $services;

    public function __construct($weighingService, $weighingDir, $entityManager, $services)
    {
        $this->services = $services;
        $this->service = $weighingService;
        $this->weighingDir = $weighingDir;
        $this->entityManager = $entityManager;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function saveAction()
    {
        if ($this->getRequest()->isPost()) {
            if ($this->validate($this->getRequest()->getContent(), self::SAVE_ACTION)) {
                $this->service->store($this->getRequest()->getContent());
                return $this->responseSuccess([self::MESSAGE => 'Weighing has been processed']);
            } else {
                return $this->responseError([self::MESSAGE => 'unprocessable entity'], 422);
            }
        }
        return $this->responseError([self::MESSAGE => 'illegal action'], 405);
    }

    /**
     * {@inheritdoc}
     */
    public function indexAction()
    {
        $form = $this->filterForm($this->getRequest(), 'weighing');
        $params = $form->getFilterParams('weighing');

        if (! $this->getRequest()->getPost(self::START_DATE) && $this->isClearFilter()) {
            $params[self::START_DATE] = date('Y-m-d');
        }
        if (! $this->getRequest()->getPost(self::END_DATE) && $this->isClearFilter()) {
            $params[self::END_DATE] = date('Y-m-d');
        }

        $currentDepartment = $this->currentDepartment()->getDepartment();
        $params['department'] = $currentDepartment ? $currentDepartment->getId() : $params['department'];

        $weighings = $this->service->getTableList($params);
        $customers = $this->services[CustomerService::class]->getTableList([]);
        $departments = $this->services[DepartmentService::class]->findAll();

        $weighingsGrouppedByMetal = $this->service->getGrouppedByMetal($params[self::START_DATE], $params[self::END_DATE], $params['department']);

        return new ViewModel([
            'imagePath' => $this->weighingDir,
            'weighings' => json_encode($weighings),
            'customers' => json_encode($customers),
            'departments' => json_encode($departments),
            'userDepartment' => json_encode($currentDepartment),
            'form' => $form->getForm($params),
            'fullAccess' => $this->checkAccess(),
            'weighingsGrouppedByMetal' => json_encode($weighingsGrouppedByMetal),
            self::START_DATE => $params[self::START_DATE],
            self::END_DATE => $params[self::END_DATE]
        ]);
    }

    public function updateAction()
    {
        if ($this->getRequest()->isPost()) {
            $decodedContent = json_decode($this->getRequest()->getContent());

            try {
                $weighingToUpdate = $this->service->fill($decodedContent);

                $this->checkWeighingIsNotPaid($weighingToUpdate->hasBeenPaid())
                    ->checkWeighingIsCurrentDate($decodedContent->date);

                if ($this->validate($this->getRequest()->getContent(), self::UPDATE_ACTION)) {
                    $weighing = $this->service->update($weighingToUpdate);
                    return $this->responseSuccess([self::MESSAGE => 'Weighing has been processed', 'data' => $weighing
                    ]);
                }
                throw new Exception('unprocessable entity');
            } catch (Exception $e) {
                return $this->responseError([self::MESSAGE => $e->getMessage()], 422);
            }
        }
        return $this->responseError([self::MESSAGE => 'illegal action'], 405);
    }

    /**
     * @param bool $isPaid
     * @return $this
     * @throws Exception
     */
    private function checkWeighingIsNotPaid(bool $isPaid)
    {
        if ($isPaid && ! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh())) {
            throw new Exception('Данные взвешивания не могут быть изменены после оплаты');
        }
        return $this;
    }

    /**
     * @param $weighingDate
     * @return $this
     * @throws Exception
     */
    private function checkWeighingIsCurrentDate($weighingDate)
    {
        if ($weighingDate !== date('Y-m-d') && ! ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh())) {
            throw new Exception('Разрешено редактирование только за текущий день');
        }
        return $this;
    }

    /**
     * Получить полноразмерное изображение элемента взвешивания
     * @return void
     */
    public function imageFullAction()
    {
        $id = $this->params()->fromRoute('id');
        $this->service->getWeighingItemImage($id, '');
    }

    public function imagePreviewAction()
    {
        $id = $this->params()->fromRoute('id');
        $this->service->getWeighingItemImage($id, '_thumbnail');
    }

    public function deleteAction()
    {
        if ($this->checkAccess()) {
            $this->service->delete(
                $this->params()->fromRoute('id')
            );
        }
        return $this->redirect()->toRoute('weighing');
    }

    public function deleteItemAction()
    {
        if ($this->checkAccess()) {
                $this->service->deleteItem(
                    $this->params()->fromRoute('id')
                );
        }
        return $this->redirect()->toRoute('weighing');
    }

    private function validate($data, $actionType)
    {
        $decodedData = json_decode($data, true);

        $weighingItemEntity = new WeighingItem();
        $weighingItemForm = new Form();
        $weighingItemForm->setInputFilter($weighingItemEntity->getInputFilter());

        $validationFields = ['trash', 'clogging', 'metalId', 'tare', 'brutto', 'metalId', 'price'];

        if ($actionType === self::SAVE_ACTION) {
            $validationFields = array_diff($validationFields, ['price']);
            $weighingItemForm->setValidationGroup($validationFields);
        } elseif ($actionType === self::UPDATE_ACTION) {
            $validationFields = array_diff($validationFields, ['photoPreview', 'photoFull']);
            $weighingItemForm->setValidationGroup($validationFields);
        }

        foreach ($decodedData['weighings'] as $weighingItem) {
            $weighingItemForm->setData($weighingItem);

            if (! $weighingItemForm->isValid()) {
                return false;
            }
        }

        $weighingEntity = new Weighing();
        $weighingForm = new Form();
        $weighingForm->setInputFilter($weighingEntity->getInputFilter());
        $weighingForm->setData($decodedData);

        return $weighingForm->isValid();
    }

    /**
     * Получить элементы для фильтрации
     */
    protected function getFilterForm()
    {
        if ($this->currentDepartment()->getDepartment()) {
            return new SubmitElement(new DateElement($this->entityManager));
        }

        return new SubmitElement(new DepartmentElement(new DateElement($this->entityManager)));
    }

    private function checkAccess(): bool
    {
        return $this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh();
    }
}
