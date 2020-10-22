<?php
namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Helper\HasAccess;
use Core\Traits\FormatNumbers;
use Core\Traits\RestMethods;
use Doctrine\ORM\EntityManager;
use Storage\Entity\CustomerTotal;
use Storage\Form\EditDateCustomer;
use Storage\Plugin\CurrentDepartment;
use Storage\Service\CashService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\SubmitElement;
use Reference\Service\CustomerService;

/**
 * Class CashTotalController
 * @package Storage\Controller
 * @method CurrentDepartment currentDepartment()
 * @method HasAccess hasAccess() hasAccess(string $className, string $permission)
 * @method CurrentUser currentUser()
 */
class CashTotalController extends AbstractActionController
{
    use FilterableController;
    use RestMethods;
    use FormatNumbers;

    protected string $indexRoute = 'storageCashTotal';
    private CashService $cashService;
    private EntityManager $entityManager;
    private array $services;

    /**
     * CrudController constructor.
     * @param $entityManager
     * @param $services
     */
    public function __construct($entityManager, $services, $cashService)
    {
        $this->entityManager = $entityManager;
        $this->cashService = $cashService;
        $this->services = $services;
    }

    public function indexAction()
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($this->currentDepartment()->isHide()) {
            die('');
        }

        $hasInspectionDate = ($this->hasAccess(static::class, 'editDateCustomer') && $currentDepartment === null);

        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        $departmentIdFromRoute = $this->params()->fromRoute('department');
        if ($params['department'] != 0 && $departmentIdFromRoute != $params['department'] && $this->currentUser()->getDepartment() === null) {
            return $this->redirect()->toRoute($this->indexRoute, ['department' => $params['department']]);
        }

        $departmentId = $currentDepartment ? $currentDepartment->getId() : null;

        $totalData = $departmentId
            ? $this->cashService->getTotalByDepartment($departmentId, $params['enddate'])
            : $this->cashService->getTotal($params['enddate']);

        $summary = $this->cashService->getSummary($params['startdate'], $params['enddate'], $departmentId);

        return new ViewModel([
            'form' => $filterForm->getForm($params),
            'total' => $summary,
            'dep' => $departmentId,
            'route' => $this->indexRoute,
            'hasInspectionDate' => $hasInspectionDate,
            'data' => $totalData
        ]);
    }

    public function legalAction()
    {
        return new ViewModel();
    }

    public function legalDataAction(): Response
    {
        $date = $this->getRequest()->getPost('date');
        $validator = new Date();
        if (! $validator->isValid($date)) {
            return $this->responseError('Некорректная дата');
        }
        $totalData = $this->cashService->getTotal($date);
        $rows = [];
        foreach ($totalData as $customerTotal) {
            $rows[] = [
                'customer' => $customerTotal->getCustomerName(),
                'fact_total' => $this->formatCurrency($customerTotal->getFactBalance())
            ];
        }


        return $this->responseSuccess(['rows' => $rows]);
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        if ($this->currentUser()->getDepartment()) {
            return new SubmitElement(new DateElement());
        }

        return new SubmitElement(new DepartmentElement(new DateElement($this->entityManager)));
    }

    /**
     * Редактирование даты сверки
     *
     */
    public function editDateCustomerAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        $customer = $this->services[CustomerService::class]->find($id);

        $form = new EditDateCustomer($this->entityManager);
        $form->bind($customer);

        $error = '';

        if ($this->getRequest()->isPost()) {
            if (! empty($this->getRequest()->getPost('inspectionDate'))) {
                try {
                    $customer->setInspectionDate($this->getRequest()->getPost('inspectionDate'));
                    $this->services[CustomerService::class]->save($customer, $this->getRequest());
                    return $this->redirect()->toRoute($this->indexRoute);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            } else {
                $error = 'Данные не переданы';
            }
        }

        return new ViewModel([
            'error' => $error,
            'form' => $form,
            'id' => $id
        ]);
    }
}
