<?php

namespace Finance\Controller;

use Core\Traits\RestMethods;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Finance\Entity\TraderReceipts;
use Finance\Service\BankService;
use Finance\Service\TraderReceiptsService;
use Reference\Service\TraderService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\I18n\View\Helper\CurrencyFormat;

class TraderReceiptsController extends AbstractActionController
{
    use RestMethods;
    use RouteParams;

    private EntityManager $entityManager;
    private TraderReceiptsService $traderReceiptsService;
    private BankService $bankService;
    private TraderService $traderService;

    protected string $indexRoute = 'traderReceipts';

    /**
     * TraderReceiptsController constructor.
     * @param EntityManager $entityManager
     * @param TraderReceiptsService $traderReceiptsService
     * @param BankService $bankService
     * @param TraderService $traderService
     */
    public function __construct($entityManager, $traderReceiptsService, $bankService, $traderService)
    {
        $this->entityManager = $entityManager;
        $this->traderReceiptsService = $traderReceiptsService;
        $this->bankService = $bankService;
        $this->traderService = $traderService;
    }

    public function indexAction(): ViewModel
    {
        $bankAccounts = $this->bankService->findAll();
        $traders = $this->traderService->findAll();

        return new ViewModel([
            'apiUrl' => $this->indexRoute,
            'bankAccounts' => $bankAccounts,
            'traders' => $traders,
            'permissions' => $this->getPermissions()
        ]);
    }

    public function jsonAction(): Response
    {
        $dateFrom = $this->params()->fromPost('startdate');
        $dateTo = $this->params()->fromPost('enddate');
        $bank = (int)$this->params()->fromPost('bank');
        $trader = (int)$this->params()->fromPost('trader');
        $type = $this->params()->fromPost('type');

        $rows = $this->traderReceiptsService->findBy($dateFrom, $dateTo, $bank, $trader, $type);
        $sum = array_reduce($rows, fn ($result, TraderReceipts $item) => $result + $item->getMoney(), 0);

        $currencyFormatter = new CurrencyFormat();
        $json = [
            'rows' => $rows,
            'sum' => $currencyFormatter($sum, 'RUR', null, 'ru_RU')
        ];
        return $this->responseSuccess($json);
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
            $row = $id > 0 ? $this->traderReceiptsService->find($id) : new TraderReceipts();
            $form = new Form();
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            $msg = '';
            if ($form->isValid()) {
                $postData = $this->getRequest()->getPost();
                $row->setDate($postData['date']);
                $row->setMoney($postData['money']);
                $row->setBank($this->bankService->getReference($postData['bank']));
                $row->setTrader($this->traderService->getReference($postData['trader']));
                $row->setType($postData['type']);
                $this->traderReceiptsService->save($row, $this->getRequest());
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
        $this->traderReceiptsService->remove($id);
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
