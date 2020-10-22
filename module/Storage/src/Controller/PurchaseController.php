<?php

namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\QRElement;
use Application\Helper\HasAccess;
use DateTime;
use InvalidArgumentException;
use LogicException;
use Reference\Entity\Metal;
use Storage\Plugin\CurrentDepartment;
use Core\Traits\RestMethods;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use Exception;
use Reports\Service\RemoteSkladService;
use Storage\Service\WeighingService;
use Storage\Entity\Purchase;
use Storage\Entity\PurchaseDeal;
use Storage\Form\PurchaseForm;
use Storage\Service\PurchaseDealService;
use Storage\Service\PurchaseService;
use UnexpectedValueException;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Regex;
use Zend\View\Model\ViewModel;
use Application\Form\Filter\CustomerElement;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\SubmitElement;

/**
 * Class PurchaseController
 * @package Storage\Controller
 * @method CurrentDepartment currentDepartment()
 * @method CurrentUser currentUser()
 * @method HasAccess hasAccess() hasAccess(string $className, string $permission)
 */
class PurchaseController extends AbstractActionController
{
    use FilterableController;
    use RestMethods;
    use RouteParams;

    private const MESSAGE = 'message';

    private PurchaseForm $purchaseForm;
    private EntityManager $entityManager;
    private PurchaseDealService $purchaseDealService;
    private RemoteSkladService $remoteSkladService;
    private WeighingService $weighingService;

    protected PurchaseService $purchaseService;
    protected string $indexRoute = 'purchase';
    protected string $storageType = 'black';

    public function __construct($entityManager, $purchaseService, $services, $purchaseForm)
    {
        $this->purchaseForm = $purchaseForm;
        $this->entityManager = $entityManager;
        $this->purchaseService = $purchaseService;
        $this->purchaseDealService = $services[PurchaseDealService::class];
        $this->remoteSkladService = $services[RemoteSkladService::class];
        $this->weighingService = $services[WeighingService::class];
    }

    public function totalAction()
    {
        return $this->indexAction();
    }

    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        $departmentFromRoute = (int)$this->params()->fromRoute('department');
        $userHasDepartment = $this->currentUser()->getDepartment() !== null;
        $departmentFromForm = (int)$params['department'];
        if ($departmentFromForm !== 0 && $departmentFromRoute !== $departmentFromForm && ! $userHasDepartment) {
            return $this->redirect()->toRoute($this->indexRoute, ['department' => $params['department']]);
        }

        $currentDepartment = $this->currentDepartment()->getDepartment();
        $data = $this->purchaseService->getTableListData($params, $currentDepartment, $this->storageType);

        return new ViewModel([
            'data' => $data,
            'metalExpenseRoute' => 'storageMetalExpense',
            'indexRoute' => $this->indexRoute,
            'permissions' => $this->getPermissions(),
            'form' => $filterForm->getForm($params),
            'dep' => $currentDepartment ? $currentDepartment->getId() : null
        ]);
    }

    public function addAction(): ViewModel
    {
        $row = new Purchase();

        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('CurrentDepartment can\'t be null');
        }
        $row->setDepartment($currentDepartment);

        $form = $this->purchaseForm;
        $form->bind($row);

        $isBlack = $currentDepartment->isBlack();
        $metals = $this->entityManager->getRepository(Metal::class)->findByGroupType($isBlack);

        return new ViewModel([
            'title' => 'Добавление прихода',
            'form' => $form,
            'purchaseUrl' => $this->url()->fromRoute('purchase', ['department' => $currentDepartment->getId()]),
            'departmentId' => $currentDepartment->getId(),
            'metals' => $metals,
            'permissions' => $this->getPermissions()
        ]);
    }

    public function editAction()
    {
        $id = $this->getRouteId();

        $row = $this->purchaseService->find($id);

        if ((! $this->getPermissions()['delete']) && ($row->getDate() !== date('Y-m-d'))) {
            return null;
        }
        $currentDepartment = $this->currentDepartment()->getDepartment();
        $departmentId = $currentDepartment ? $currentDepartment->getId() : null;

        $form = $this->purchaseForm;
        $form->bind($row);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                if ($this->getPermissions()['delete'] || $row->getDate() === date('Y-m-d')) {
                    $row->setDepartment($currentDepartment);
                    $this->purchaseService->save($row);
                }
                return $this->redirect()->toRoute($this->indexRoute, ['department' => $departmentId]);
            }
        }

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'indexRoute' => $this->indexRoute,
            'title' => 'Редактировать приход металла',
            'form' => $form,
            'dep' => $departmentId, 'id' => $id,
        ]);
    }

    public function saveAjaxAction(): Response
    {
        try {
            if (! $this->getRequest()->isPost()) {
                throw new LogicException('Method Not Allowed');
            }
            $postData = json_decode($this->getRequest()->getContent(), true);
            if (empty($postData['items'])) {
                throw new InvalidArgumentException('Purchases empty');
            }

            $currentDepartment = $this->currentDepartment()->getDepartment();
            if ($currentDepartment === null) {
                throw new LogicException('Department is null');
            }
            $purchaseDate = ($this->currentUser()->isAdmin() || $this->currentUser()->isGlavbuh()) ? $postData['date'] : date('Y-m-d');
            if ($this->hasAccess(self::class, 'delete') || ! $currentDepartment->isBlack() || $currentDepartment->isHide()) {
                $checked = true;
            } else {
                $existsPurchaseList = $this->purchaseService->getGroupPurchaseByDay($purchaseDate, $currentDepartment->getId());
                $exportPurchaseList = $this->remoteSkladService->getMassArray($purchaseDate, $purchaseDate, $currentDepartment->getName());
                $checked = $this->purchaseService->checkEqualDayAndExportPurchase($postData['items'], $exportPurchaseList, $existsPurchaseList);
            }

            if (! $checked) {
                throw new LogicException('Указанный вес не совпадает с весовой. ');
            }
            $comment = $postData['comment'];
            $validator = new Regex(['pattern' => '/^[a-zA-Zа-яА-Я0-9.,% \/\*()\-"@]{1,255}$/ui']);
            if (! empty(trim($comment)) && ! $validator->isValid($comment)) {
                throw new InvalidArgumentException('Некорректный комментарий сделки');
            }
            $purchaseCollection = $this->purchaseService->makeEntityList($postData['items'], $purchaseDate, $currentDepartment);
            $code = md5((new DateTime())->format('Y-m-d H:i:s') . $currentDepartment->getName());
            $deal = new PurchaseDeal($code, $comment, $purchaseCollection);

            $this->purchaseDealService->save($deal);
            return $this->responseSuccess();
        } catch (Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * @return Response
     */
    public function saveByWeighingAction(): Response
    {
        try {
            if (! $this->getRequest()->isPost()) {
                throw new LogicException('Method Not Allowed');
            }
            $decodedContent = json_decode($this->getRequest()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $currentDepartment = $this->currentDepartment()->getDepartment();
            if ($currentDepartment !== null && $currentDepartment->getId() !== $decodedContent['departmentId']) {
                throw new LogicException('Данное подразделение не доступно для оформления');
            }

            if ($currentDepartment !== null && $decodedContent['date'] !== date('Y-m-d')) {
                throw new UnexpectedValueException('Введена не текущая дата');
            }

            $purchasesByDateAndDepartment = $this->purchaseService->findByDateAndDepartment($decodedContent['date'], $decodedContent['departmentId']);
            if (count($purchasesByDateAndDepartment) !== 0) {
                throw new LogicException('На заданную дату был оформлен приход');
            }

            $weighings = $this->weighingService->getAggregateWeighings($decodedContent['date'], $decodedContent['departmentId']);
            if (! $weighings) {
                throw new LogicException('По данному подразделению не найдено ни одного неоформленного взвешивания');
            }

            foreach ($weighings as $groupByCustomer) {
                foreach ($groupByCustomer as $groupedByMetal) {
                    foreach ($groupedByMetal as $weighing) {
                        $purchase = new Purchase();
                        $purchase->setDepartment($weighing['department']);
                        $purchase->setDate($decodedContent['date']);
                        $purchase->setCustomer($weighing['customer']);
                        $purchase->setMetal($weighing['metal']);
                        $purchase->setCost($weighing['price']);
                        $purchase->setWeight($weighing['mass']);

                        $this->purchaseService->save($purchase);
                    }
                }
            }
            return $this->responseSuccess();
        } catch (Exception $e) {
            return $this->responseError([self::MESSAGE => $e->getMessage()]);
        }
    }

    /**
     * Экшн удаления записи
     * @return Response
     */
    public function deleteAction(): Response
    {
        $id = $this->getRouteId();
        $this->purchaseService->remove($id);
        return $this->redirect()->toRoute($this->indexRoute, ['department' => $this->currentDepartment()->getId()]);
    }

    protected function getPermissions(): array
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
            'deal' => $this->hasAccess(static::class, 'deal')
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterForm()
    {
        if ($this->currentUser()->getDepartment()) {
            return new SubmitElement(new QRElement(new CustomerElement(new DateElement($this->entityManager))));
        }
        return new SubmitElement(new DepartmentElement(new QRElement(new CustomerElement(new DateElement($this->entityManager)))));
    }
}
