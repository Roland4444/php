<?php

namespace OfficeCash\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\CategoryElement;
use Application\Form\Filter\CommentElement;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Application\Helper\HasAccess;
use Core\Entity\ExpenseDay;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Reference\Entity\Department;
use Reference\Service\CategoryService;
use Reference\Service\DepartmentService;
use OfficeCash\Entity\OtherExpense;
use OfficeCash\Form\OtherExpenseForm;
use OfficeCash\Service\OtherExpenseService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class OfficeOtherExpenseController
 * @package OfficeCash\Controller
 * @method CurrentUser currentUser()
 * @method HasAccess hasAccess() hasAccess(string $className, string $permission)
 */
class OfficeOtherExpenseController extends AbstractActionController
{
    use FilterableController;
    use RouteParams;

    private const MIN_EDIT_DATE = '-1 day';

    private EntityManager $entityManager;
    private OtherExpenseService $expenseService;
    private DepartmentService $departmentService;
    private CategoryService $categoryService;

    protected string $indexRoute = 'office_other_expense';

    public function __construct($entityManager, $expenseService, $departmentService, $categoryService)
    {
        $this->entityManager = $entityManager;
        $this->expenseService = $expenseService;
        $this->departmentService = $departmentService;
        $this->categoryService = $categoryService;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCurrentDepartment(): Department
    {
        return $this->departmentService->findByAlias('officecash');
    }

    /**
     * {@inheritdoc}
     */
    protected function getIndexViewParams()
    {
        return [
            'route' => $this->indexRoute,
            'currentDepartmentId' => $this->getCurrentDepartment()->getId()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
            'editPartial' => $this->hasAccess(static::class, 'editPartial'),
        ];
    }

    /**
     * @return ViewModel
     * @throws Exception
     */
    public function indexAction(): ViewModel
    {
        $currentDepartment = $this->getCurrentDepartment();

        $filterForm = $this->filterForm($this->getRequest(), $this->indexRoute);
        $params = $filterForm->getFilterParams($this->indexRoute);

        $this->expenseService->setDates($params['startdate'], $params['enddate']);
        $this->expenseService->setDepartment($currentDepartment->getId());
        $this->expenseService->setCategory($params['category']);
        $this->expenseService->setComment($params['comment']);

        $rows = $this->expenseService->findAll();

        $dates = [];
        $res = [];
        $day = null;
        $before_date = '';
        /** @var OtherExpense $row */
        foreach ($rows as $row) {
            if (! in_array($row->getDate(), $dates, true)) {
                $dates[] = $row->getDate();
                $day = new ExpenseDay($row->getDate());
            }
            $day->addExpense($row);
            if ($row->getDate() !== $before_date) {
                $before_date = $row->getDate();
                $res[] = $day;
            }
        }

        $sum = $this->expenseService->getSum();

        $minEditDate = new \DateTime();
        $minEditDate->modify(self::MIN_EDIT_DATE);
        $minEditDate->setTime(0, 0);

        return new ViewModel([
            'permissions' => $this->getPermissions(),
            'params' => $this->getIndexViewParams(),
            'rows' => $rows,
            'res' => $res,
            'sum' => $sum,
            'form' => $filterForm->getForm($params),
            'dep' => $currentDepartment->getId(),
            'minEditDate' => $minEditDate
        ]);
    }

    /**
     * @return Response|ViewModel
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function addAction()
    {
        $currentDepartment = $this->getCurrentDepartment();

        $roleIds = $this->currentUser()->getRoleIds();
        $form = new OtherExpenseForm($this->entityManager, $roleIds);
        $row = new OtherExpense();

        try {
            $row->setDepartment($currentDepartment);
            $row->setCategory($this->categoryService->findDefaultByOption());
        } catch (NoResultException $e) {
            throw new Exception('Заполните необходимые справочники');
        }
        $form->bind($row);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                if (! $this->hasAccess(static::class, 'delete')) {
                    $row->setDate(date('Y-m-d'));
                }
                $this->expenseService->save($row);

                return $this->redirect()->toRoute($this->indexRoute);
            }
        }

        return new ViewModel([
            'params' => $this->getIndexViewParams(),
            'title' => 'Добавить управленческий расход',
            'form' => $form,
            'action' => 'add',
            'id' => null,
            'permissions' => $this->getPermissions(),
        ]);
    }

    /**
     * @return Response|ViewModel
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function editAction()
    {
        $id = $this->getRouteId();
        $row = $this->expenseService->find($id);

        $roleIds = $this->currentUser()->getRoleIds();
        $form = new OtherExpenseForm($this->entityManager, $roleIds);
        $form->bind($row);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->expenseService->save($row);
                return $this->redirect()->toRoute($this->indexRoute);
            }
        }

        return new ViewModel([
            'title' => 'Редактировать управленческий расход',
            'params' => $this->getIndexViewParams(),
            'form' => $form,
            'action' => 'edit',
            'id' => $id,
            'permissions' => $this->getPermissions(),
        ]);
    }

    /**
     * Ограничивает возможности пользователя на изменение информации
     *
     * @throws Exception
     */
    public function editPartialAction()
    {
        $id = $this->getRouteId();
        $row = $this->expenseService->find($id);

        if (empty($row)) {
            return $this->redirect()->toRoute($this->indexRoute);
        }

        $minDate = new \DateTime();
        $minDate->modify(self::MIN_EDIT_DATE);
        $minDate->setTime(0, 0);
        $rowDate = new \DateTime($row->getDate());

        if ($minDate > $rowDate) {
            return $this->redirect()->toRoute($this->indexRoute);
        }

        if ($this->getRequest()->isPost()) {
            $datePost = new \DateTime($this->getRequest()->getPost('date'));
            if ($rowDate != $datePost) {
                return $this->redirect()->toRoute($this->indexRoute);
            }
        }
        return $this->editAction();
    }

    /**
     * Экшн удаления записи
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function deleteAction(): Response
    {
        $id = $this->getRouteId();
        $this->expenseService->remove($id);
        return $this->redirect()->toRoute($this->indexRoute);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterForm()
    {
        $user = $this->currentUser();
        return new SubmitElement(new CommentElement(new CategoryElement(new DateElement($this->entityManager), $user->getRoleIds())));
    }
}
