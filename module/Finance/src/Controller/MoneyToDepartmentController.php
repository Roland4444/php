<?php

namespace Finance\Controller;

use Core\Traits\RestMethods;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Finance\Entity\MoneyToDepartment;
use Finance\Service\MoneyToDepartmentService;
use Finance\Service\BankService;
use Reference\Service\DepartmentService;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;
use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\Mvc\Controller\AbstractActionController;

class MoneyToDepartmentController extends AbstractActionController
{
    use RestMethods;

    private AuthenticationService $authService;
    private MoneyToDepartmentService $moneyToDepartmentService;
    private BankService $bankService;
    private DepartmentService $departmentService;

    protected string $indexRoute = 'moneyToDepartment';

    public function __construct($departmentService, $authService, $moneyToDepartmentService, $bankService)
    {
        $this->authService = $authService;
        $this->moneyToDepartmentService = $moneyToDepartmentService;
        $this->bankService = $bankService;
        $this->departmentService = $departmentService;
    }

    public function indexAction(): ViewModel
    {
        $bankAccounts = $this->bankService->findAll();
        $departments = $this->departmentService->findOpened(false);

        return new ViewModel([
            'apiUrl' => $this->indexRoute,
            'bankAccounts' => $bankAccounts,
            'departments' => $departments,
            'permissions' => $this->getPermissions()
        ]);
    }

    public function jsonAction(): Response
    {
        $params = $this->params()->fromPost();
        [$dateFrom, $dateTo, $departmentId, $bankId] = [$params['startdate'], $params['enddate'], $params['department'], $params['bank']];

        $rows = $this->moneyToDepartmentService->findByParams($dateFrom, $dateTo, $departmentId, $bankId);

        $user = $this->authService->getIdentity();
        $currencyFormatter = new CurrencyFormat();
        $sum = 0;
        foreach ($rows as $row) {
            if (! $user->isAdmin() && $row->getDepartment()->isHide()) {
                continue;
            }
            $sum += $row->getMoney();
        }
        return $this->responseSuccess([
            'rows' => $rows,
            'sum' => $currencyFormatter($sum, 'RUR', null, 'ru_RU')
        ]);
    }

    /**
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function saveAction(): Response
    {
        $id = (int)$this->getRequest()->getPost('id');

        if ($this->getRequest()->isPost()) {
            $row = $id > 0 ? $this->moneyToDepartmentService->find($id) : new MoneyToDepartment();
            $form = new Form();
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            $msg = '';
            if ($form->isValid()) {
                $postData = $this->getRequest()->getPost();
                $row->setDate($postData['date']);
                $row->setMoney($postData['money']);
                $row->setVerified($postData['verified']);
                $row->setDepartment($this->departmentService->getReference($postData['department']));
                $row->setBank($this->bankService->getReference($postData['bank']));
                $this->moneyToDepartmentService->save($row, $this->getRequest());
                return $this->responseSuccess();
            }
            foreach ($form->getMessages() as $key => $message) {
                $msg .= 'Поле ' . $key . ' заполнено не корректно<br />';
            }
            return $this->responseError($msg);
        }
        return $this->responseError('Method Not Allowed', Response::STATUS_CODE_405);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function deleteAction(): void
    {
        $id = $this->getRequest()->getPost('id');
        $this->moneyToDepartmentService->remove($id);
    }

    /**
     * Пометить выдачу денег как проверреную
     */
    public function verifyAction(): Response
    {
        try {
            $id = $this->getRequest()->getPost('id');
            $row = $this->moneyToDepartmentService->find($id);
            $row->setVerified(true);
            $this->moneyToDepartmentService->save($row);
            return $this->responseSuccess(['result' => 'ok']);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    private function getPermissions(): array
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }
}
