<?php

namespace Finance\Controller;

use Application\Helper\HasAccess;
use Application\Service\AccessService;
use Core\Traits\RestMethods;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use Finance\Entity\CashTransfer;
use Finance\Entity\OtherExpense;
use Finance\Form\OtherExpenseForm;
use Finance\Service\BankService;
use Finance\Service\OtherExpenseService;
use JsonException;
use Reference\Entity\User;
use Reference\Service\CategoryService;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;
use Application\Form\Filter\DateElement;
use Finance\Form\Filter\BankElement;
use Application\Form\Filter\CommentElement;
use Application\Form\Filter\CategoryElement;
use Application\Form\Filter\SubmitElement;

/**
 * Class OtherExpenseController
 * @package Finance\Controller
 * @method HasAccess hasAccess($className, $permission)
 */
class OtherExpenseController extends AbstractActionController
{
    use RestMethods;
    use RouteParams;

    protected bool $withoutCash = false;

    private BankService $bankService;
    private CategoryService $categoryService;
    private EntityManager $entityManager;
    private AuthenticationService $authService;
    private OtherExpenseService $otherExpenseService;
    private AccessService $accessService;

    protected string $indexRoute = 'mainOtherExpense';

    /**
     * OtherExpenseController constructor.
     * @param $entityManager
     * @param $bankService
     * @param $categoryService
     * @param $otherExpenseService
     * @param $authService
     * @param $accessService
     */
    public function __construct(
        $entityManager,
        $bankService,
        $categoryService,
        $otherExpenseService,
        $authService,
        $accessService
    ) {
        $this->entityManager = $entityManager;
        $this->bankService = $bankService;
        $this->categoryService = $categoryService;
        $this->otherExpenseService = $otherExpenseService;
        $this->authService = $authService;
        $this->accessService = $accessService;
    }

    public function indexAction(): ViewModel
    {
        $bankAccounts = $this->bankService->findAll();
        $user = $this->authService->getIdentity();
        $categories = $this->categoryService->findByRole($user->getRoleIds());

        return new ViewModel([
            'apiUrl' => $this->indexRoute,
            'bankAccounts' => $bankAccounts,
            'categories' => $categories,
            'permissions' => $this->getPermissions()
        ]);
    }

    public function jsonAction(): Response
    {
        $requestParams = $this->params()->fromPost();

        $rows = $this->otherExpenseService->setParams($requestParams)->findAll($this->withoutCash);
        $json = $this->otherExpenseService->makeArrayOfDay($rows);
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
            $row = $id > 0 ? $this->otherExpenseService->find($id) : new OtherExpense();
            $form = new OtherExpenseForm($this->entityManager, $this->accessService);
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            $msg = '';
            if ($form->isValid()) {
                $postData = $this->getRequest()->getPost();
                $row->setRecipient($postData['recipient']);
                $row->setDate($postData['date']);
                $row->setInn($postData['inn']);
                $row->setComment($postData['comment']);
                $row->setRealdate($postData['realdate']);
                $row->setCategory($this->categoryService->getReference($postData['category']));
                $row->setBank($this->bankService->getReference($postData['bank']));
                $row->setMoney($postData['money']);
                $this->otherExpenseService->save($row, $this->getRequest());
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
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws JsonException
     */
    public function deleteAction(): Response
    {
        $id = $this->getRequest()->getPost('id');
        $this->otherExpenseService->remove($id);
        return $this->responseSuccess();
    }

    private function getPermissions(): array
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
            'import' => $this->hasAccess(static::class, 'import'),
            'template' => $this->hasAccess(static::class, 'template'),
        ];
    }
}
