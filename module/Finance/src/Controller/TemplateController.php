<?php

namespace Finance\Controller;

use Application\Exception\ServiceException;
use Core\Traits\RouteParams;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Finance\Entity\Template;
use Finance\Form\TemplateForm;
use Finance\Service\BankClientService;
use Finance\Service\TemplateService;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TemplateController extends AbstractActionController
{
    use RouteParams;

    private string $indexRoute = 'financeImport';

    private TemplateService $templateService;
    private BankClientService $bankClientService;
    private EntityManager $entityManager;
    private AuthenticationService $authService;

    public function __construct($templateService, $bankClientService, $entityManager, $authService)
    {
        $this->templateService = $templateService;
        $this->bankClientService = $bankClientService;
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    /**
     * @return ViewModel
     * @throws
     */
    public function indexAction(): ViewModel
    {
        $messages = [];
        if ($this->getRequest()->isPost()) {
            $files = $this->getRequest()->getFiles();

            if (! empty($files['file'])) {
                try {
                    $data = $this->bankClientService->readFromRequest($files);
                    $currentUser = $this->authService->getIdentity();
                    $messages = $this->bankClientService->savePayments($data, ($currentUser->isAdmin() || $currentUser->isGlavbuh()));
                } catch (ServiceException $e) {
                    $errorMsg = $e->getMessage();
                }
            }
        }

        $templates = $this->templateService->findAll();

        return new ViewModel([
            'messages' => $messages,
            'error' => $errorMsg ?? null,
            'templates' => $templates,
            'permissions' => $this->getPermissions(),
            'indexRoute' => $this->indexRoute
        ]);
    }

    /**
     * @return Response|ViewModel
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addAction()
    {
        $form = new TemplateForm($this->entityManager);
        $row = new Template();

        $form->bind($row);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->templateService->save($row);
                return $this->redirect()->toRoute($this->indexRoute);
            }
        }

        return new ViewModel([
            'title' => 'Добавить шаблон',
            'form' => $form,
        ]);
    }

    /**
     * @return Response|ViewModel
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException]
     */
    public function editAction()
    {
        $id = $this->getRouteId();
        $row = $this->templateService->find($id);

        $form = new TemplateForm($this->entityManager);
        $form->bind($row);

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($row->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->templateService->save($row);
                return $this->redirect()->toRoute($this->indexRoute);
            }
        }

        return new ViewModel([
            'title' => 'Редактировать управленческий расход',
            'form' => $form,
        ]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function deleteAction(): Response
    {
        $id = $this->getRouteId();
        $this->templateService->remove($id);
        return $this->redirect()->toRoute($this->indexRoute);
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
