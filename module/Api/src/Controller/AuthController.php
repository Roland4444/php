<?php

namespace Api\Controller;

use Application\Service\AuthLog;
use Core\Traits\RestMethods;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\User;
use Reference\Form\UserForm;
use Reference\Service\UserService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Stdlib\ResponseInterface;

class AuthController extends AbstractActionController
{
    use RestMethods;

    private EntityManager $entityManager;
    private AuthenticationServiceInterface $authService;
    private AuthLog $logService;
    private UserService $userService;

    public function __construct($em, $authService, $logService, $userService)
    {
        $this->entityManager = $em;
        $this->authService = $authService;
        $this->logService = $logService;
        $this->userService = $userService;
    }

    /**
     * Получение токена для работы с api
     * @return Response|ResponseInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function tokenAction()
    {
        if ($this->getRequest()->isGet()) {
            return $this->redirect()->toRoute('login');
        }

        $form = new UserForm($this->entityManager);
        $form->setHydrator(new DoctrineObject($this->entityManager))->setObject(new User());
        $form->addElements();
        $form->setValidationGroup(['login']);
        $data = $this->getRequest()->getPost();
        $form->setData($data);

        if ($form->isValid()) {
            $adapter = $this->authService->getAdapter();
            $adapter->setIdentity($data['login']);
            $adapter->setCredential($data['password']);
            $authenticate = $this->authService->authenticate();
            if ($authenticate->isValid()) {
                $this->userService->resetAttempts($data['login']);
                $this->logService->log($data['login'], 1);

                $tokenData = $this->userService->saveToken($data['login']);
                $session = new Container();
                $session->check = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

                return $this->responseSuccess([
                    'token' => $tokenData['token'],
                    'expired' => $tokenData['expired']
                ]);
            }

            $this->logService->log($data['login']);
            $this->userService->blockUser($data['login']);
            return $this->responseError('Wrong username or password', Response::STATUS_CODE_403);
        }
        return $this->responseError('Incorrect input data');
    }
}
