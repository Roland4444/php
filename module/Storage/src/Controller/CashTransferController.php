<?php

namespace Storage\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\FromToElement;
use Application\Form\Filter\SubmitElement;
use Application\Helper\HasAccess;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use LogicException;
use Storage\Entity\CashTransfer;
use Storage\Form\CashTransferForm;
use Storage\Plugin\CurrentDepartment;
use Storage\Service\CashTransferService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class CashTransferController
 * @package Storage\Controller
 * @method CurrentUser currentUser()
 * @method CurrentDepartment currentDepartment()
 * @method HasAccess hasAccess() hasAccess(string $className, string $permission)
 */
class CashTransferController extends AbstractActionController
{
    use FilterableController;
    use RouteParams;

    private CashTransferService $cashTransferService;
    private EntityManager $entityManager;

    public function __construct($container)
    {
        $this->cashTransferService = $container->get(CashTransferService::class);
        $this->entityManager = $container->get('entityManager');
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new FromToElement(new DateElement($this->entityManager)));
    }

    public function indexAction(): ViewModel
    {
        $form = $this->filterForm($this->getRequest(), 'storageCashTransfer');
        $params = $form->getFilterParams('storageCashTransfer');

        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        $rows = $this->cashTransferService->findBy($params, $currentDepartment->getId());
        $money = array_reduce($rows, fn($result, CashTransfer $row) => $result + $row->getMoney());

        $depId = $currentDepartment->getId();

        return new ViewModel([
            'rows' => $rows,
            'money' => $money,
            'form' => $form->getForm($params),
            'dep' => $depId,
        ]);
    }

    /**
     * @return Response|ViewModel
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addAction()
    {
        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        $form = new CashTransferForm($this->entityManager, $currentDepartment);
        $row = new CashTransfer();

        $form->bind($row);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                if (! $this->hasAccess(__CLASS__, 'delete')) {
                    $row->setDate(date('Y-m-d'));
                }
                $row->setSource($currentDepartment);
                $this->cashTransferService->save($row);

                return $this->redirect()->toRoute('storageCashTransfer', ['department' => $currentDepartment->getId()]);
            }
        }

        return new ViewModel([
            'title' => 'Добавить расход в кассу',
            'form' => $form,
            'dep' => $currentDepartment->getId(),
            'action' => 'add',
            'id' => null,
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

        $row = $this->cashTransferService->find($id);

        $currentDepartment = $this->currentDepartment()->getDepartment();
        if ($currentDepartment === null) {
            throw new LogicException('Current department can\'t be null');
        }
        $form = new CashTransferForm($this->entityManager, $currentDepartment);
        $form->bind($row);
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->cashTransferService->save($row);
                return $this->redirect()->toRoute('storageCashTransfer', ['department' => $currentDepartment->getId()]);
            }
        }

        return new ViewModel([
            'title' => 'Редактировать расход в кассу',
            'form' => $form,
            'dep' => $currentDepartment->getId(),
            'action' => 'edit',
            'id' => $id
        ]);
    }

    /**
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function deleteAction(): Response
    {
        $id = $this->getRouteId();
        $this->cashTransferService->remove($id);
        return $this->redirect()->toRoute('storageCashTransfer', ['department' => $this->currentDepartment()->getId()]);
    }
}
