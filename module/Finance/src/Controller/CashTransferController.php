<?php

namespace Finance\Controller;

use Core\Traits\RestMethods;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Finance\Entity\CashTransfer;
use Finance\Service\BankService;
use Finance\Service\CashTransferService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;
use Zend\I18n\View\Helper\CurrencyFormat;
use Zend\Mvc\Controller\AbstractActionController;

class CashTransferController extends AbstractActionController
{
    use RestMethods;
    use RouteParams;

    private EntityManager $entityManager;
    private CashTransferService $cashTransferService;
    private BankService $bankService;

    protected string $indexRoute = 'cashTransfer';

    public function __construct($entityManager, $cashTransferService, $bankService)
    {
        $this->entityManager = $entityManager;
        $this->cashTransferService = $cashTransferService;
        $this->bankService = $bankService;
    }

    public function indexAction(): ViewModel
    {
        $bankAccounts = $this->bankService->findAll();

        return new ViewModel([
            'apiUrl' => $this->indexRoute,
            'bankAccounts' => $bankAccounts,
            'permissions' => $this->getPermissions()
        ]);
    }

    public function jsonAction(): Response
    {
        $dateFrom = $this->params()->fromPost('startdate');
        $dateTo = $this->params()->fromPost('enddate');
        $source = (int)$this->params()->fromPost('source');
        $dest = (int)$this->params()->fromPost('dest');

        $rows = $this->cashTransferService->getByParams($dateFrom, $dateTo, $source, $dest);
        $sum = array_reduce($rows, fn ($result, CashTransfer $item) => $result + $item->getMoney(), 0);
        $currencyFormatter = new CurrencyFormat();
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
            $row = $id > 0 ? $this->cashTransferService->find($id) : new CashTransfer();
            $form = new Form();
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            $msg = '';
            if ($form->isValid()) {
                $postData = $this->getRequest()->getPost();
                $row->setDate($postData['date']);
                $row->setMoney($postData['money']);
                $row->setSource($this->bankService->getReference($postData['source']));
                $row->setDest($this->bankService->getReference($postData['dest']));
                $this->cashTransferService->save($row, $this->getRequest());
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
        $this->cashTransferService->remove($id);
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
