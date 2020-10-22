<?php
namespace Reference\Controller;

use Application\Service\AuthLog;
use Reference\Entity\User;
use Reference\Form\UserForm;
use Reference\Service\UserService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\Http\Response;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

/**
 * Class UserController
 * @package Reference\Controller
 */
class UserController extends PlainReferenceController
{
    protected $routeIndex = 'user';

    protected AuthLog $logService;
    protected AuthenticationService $authService;
    private SessionManager $sessionManager;

    /**
     * UserController constructor.
     * @param UserService $service
     * @param UserForm $form
     * @param AuthLog $logService
     * @param AuthenticationService $authService
     * @param SessionManager $sessionManager
     */
    public function __construct(UserService $service, UserForm $form, $logService, $authService, $sessionManager)
    {
        $this->entityInstance = new User();

        $this->service = $service;
        $this->form = $form;
        $this->logService = $logService;
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Login action
     * @return Response|ViewModel
     * @throws \Exception
     */
    public function loginAction()
    {
        if ($this->logService->inBlackList($_SERVER['REMOTE_ADDR'])) {
            die('.');
        }
        if ($this->authService->getIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        /** @var UserForm $form */
        $form = $this->getForm();

        $form->setValidationGroup('login');

        $message = '';

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $data = $this->getRequest()->getPost();

                $adapter = $this->authService->getAdapter();
                $adapter->setIdentity($data['login']);
                $adapter->setCredential($data['password']);

                if ($data['remember'] === 'yes') {
                    $this->sessionManager->rememberMe(60 * 60 * 24 * 30);
                }
                $authenticate = $this->authService->authenticate();
                if ($authenticate->isValid()) {
                    $session = new Container();
                    $session->check = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
                    $this->getService()->resetAttempts($data['login']);

                    $this->logService->log($data['login'], 1);

                    return $this->redirect()->toRoute('home');
                } else {
                    $login = $this->getRequest()->getPost('login');
                    $this->logService->log($login);

                    $this->getService()->blockUser($login);

                    $message = 'Неверный логин или пароль, либо учетная запись заблокированна';
                }
            } else {
                $message = 'Введены некорректные данные';
            }
        }

        if (array_key_exists('blocked', $this->getRequest()->getQuery()->toArray())) {
            $message = 'Учетная запись заблокированна';
        }

        $view = new ViewModel([
            'title' => 'Вход',
            'form' => $form,
            'message' => $message,
        ]);
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Logout
     * @return Response
     */
    public function logoutAction()
    {
        $this->authService->clearIdentity();
        return $this->redirect()->toRoute('login');
    }

    /**
     * Change password
     *
     * @return Response|ViewModel
     * @throws \Exception
     */
    public function changepassAction()
    {
        /** @var UserForm $form */
        $form = $this->getForm();
        $form->addElements();

        $ignoreElements = ['name', 'login', 'roles', 'department', 'change_password', 'is_blocked', 'login_from_internet'];
        foreach ($ignoreElements as $elementName) {
            $form->remove($elementName);
        }
        $form->setValidationGroup('password', 'confirm_password');

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($this->entityInstance->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                /** @var User $user */
                $user = $this->getService()->find($this->authService->getIdentity()->getId());
                $user->setPassword($form->get('password')->getValue());
                $this->getService()->save($user, $this->getRequest());
                return $this->redirect()->toRoute($this->routeIndex);
            }
        }
        return new ViewModel([
            'title' => 'Изменить пароль',
            'form' => $form,
        ]);
    }

    /**
     * Подготавливает параметры для редактирования
     *
     * @param UserForm $form
     * @param User $entity
     */
    protected function prepareFormAndEntityForEdit($form, $entity)
    {
        $form->get('password')->setValue('');

        if ($this->getRequest()->isPost()) {
            $entity->getInputFilter()->get('password')->setRequired(false);
            $entity->getInputFilter()->get('confirm_password')->setRequired(false);
        }
    }
}
