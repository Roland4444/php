<?php

namespace Finance\Controller;

use Exception;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\I18n\View\Helper\CurrencyFormat;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;

use Core\Traits\RestMethods;
use Core\Traits\RouteParams;

use Finance\Entity\OtherReceipts;
use Finance\Service\BankService;
use Finance\Service\OtherReceiptService;

class OtherReceiptsController extends AbstractActionController
{
    use RestMethods;
    use RouteParams;

    private OtherReceiptService $receiptService;
    private BankService $bankService;

    protected string $indexRoute = 'otherReceipts';

    public function __construct($receiptService, $bankService)
    {
        $this->receiptService = $receiptService;
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
            $row = $id > 0 ? $this->receiptService->find($id) : new OtherReceipts();
            $form = new Form();
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            $msg = '';
            if ($form->isValid()) {
                $postData = $this->getRequest()->getPost();
                $row->setDate($postData['date']);
                $row->setMoney($postData['money']);
                $row->setComment($postData['comment']);
                $row->setInn($postData['inn']);
                $row->setSender($postData['sender']);
                $row->setBank($this->bankService->getReference($postData['bank']));
                $this->receiptService->save($row, $this->getRequest());
                return $this->responseSuccess();
            }
            foreach ($form->getMessages() as $key => $message) {
                $msg .= 'Поле ' . $key . ' заполнено не корректно<br />';
            }
            return $this->responseError($msg);
        }
        return $this->responseError('Method Not Allowed', Response::STATUS_CODE_405);
    }

    private function getPermissions(): array
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }

    /**
     * @return Response
     */
    public function jsonAction(): Response
    {
        $dateFrom = $this->getRequest()->getPost('startdate');
        $dateTo = $this->getRequest()->getPost('enddate');
        $bankId = (int)$this->getRequest()->getPost('bank');
        $comment = $this->getRequest()->getPost('comment');

        $rows = $this->receiptService->findByParams($dateFrom, $dateTo, $bankId, $comment);

        $sum = 0;
        foreach ($rows as $row) {
            $sum += $row->getMoney();
        }
        $currencyFormatter = new CurrencyFormat();
        $json = [
            'rows' => $rows,
            'sum' => $currencyFormatter($sum, 'RUR', null, 'ru_RU')
        ];
        return $this->responseSuccess($json);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function deleteAction(): void
    {
        $id = $this->getRequest()->getPost('id');
        $this->receiptService->remove($id);
    }
}
