<?php

namespace ReferenceTest\functional\Controller;

use Application\Service\AuthLog;
use ApplicationTest\Controller\AbstractControllerTest;
use DoctrineModule\Authentication\Adapter\ObjectRepository;
use Reference\Controller\UserController;
use Reference\Entity\User;
use Reference\Form\UserForm;
use Reference\Service\UserService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Form\Element;

class UserControllerTest extends AbstractControllerTest
{
    private $loginResult;

    public function testIndexAction()
    {
        parent::authMock();
        $this->mockService();
        $this->dispatch('/reference/user', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertMatchedRouteName('user');
        $this->assertStandardParameters();
    }

    public function testIndexActionWithRows()
    {
        parent::authMock();
        $mockEntity = new User();
        $this->mockService([$mockEntity, $mockEntity]);

        $this->dispatch('/reference/user', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('index');
        $this->assertMatchedRouteName('user');
        $this->assertStandardParameters();
    }

    public function testAddActionGet()
    {
        parent::authMock();
        $this->dispatch('/reference/user/add', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertMatchedRouteName('user/add');
        $this->assertStandardParameters();
    }

    public function testAddActionPostFormInvalid()
    {
        parent::authMock();
        $this->dispatch('/reference/user/add', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('add');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/add');
    }

    public function testAddActionPostFormValid()
    {
        parent::authMock();
        $this->mockService();
        $this->mockForm();

        $this->dispatch('/reference/user/add', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/user');
        $this->assertActionName('add');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/add');
    }

    public function testEditActionGet()
    {
        parent::authMock();
        $this->mockService();
        $this->dispatch('/reference/user/edit/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/edit');
    }

    public function testEditActionPostFormInvalid()
    {
        parent::authMock();
        $this->mockService();
        $this->dispatch('/reference/user/edit/1', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('edit');
        $this->assertQuery('.main #error-message');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/edit');
    }

    public function testEditActionPostFormValid()
    {
        parent::authMock();
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/user/edit/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/user');
        $this->assertActionName('edit');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/edit');
    }

    public function testDeleteAction()
    {
        parent::authMock();
        $this->mockService();
        $this->dispatch('/reference/user/delete/1', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/reference/user');
        $this->assertActionName('delete');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/delete');
    }

    public function testLoginActionIfUserIsAuthenticated()
    {
        parent::authMock();
        $this->mockService();
        $this->dispatch('/login', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/');
        $this->assertActionName('login');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('login');
    }

    public function testLoginActionIfFormIsNotValid()
    {
        $this->mockService();
        $this->dispatch('/login', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('login');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('login');
    }

    public function testLoginActionIfFormIsValid()
    {
        $this->loginResult = Result::SUCCESS;

        $mockLogService = $this->getMockBuilder(AuthLog::class)->getMock();
        $mockLogService->expects($this->any())->method('log')->willReturn(null);
        $this->overrideService('authlogService', $mockLogService);

        $this->mockService();
        $this->mockForm();
        $this->dispatch('/login', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertActionName('login');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('login');
    }

    public function testLoginActionIfAuthenticationIsFailed()
    {
        $this->loginResult = Result::FAILURE;

        $mockLogService = $this->getMockBuilder(AuthLog::class)->getMock();
        $mockLogService->expects($this->any())->method('log')->willReturn(null);
        $this->overrideService('authlogService', $mockLogService);

        $this->mockService();
        $this->mockForm();
        $this->dispatch('/login', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('login');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('login');
    }

    public function testLogoutAction()
    {
        parent::authMock();
        $this->mockService();
        $this->dispatch('/reference/user/logout', 'GET');
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/login');
        $this->assertActionName('logout');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/logout');
    }

    public function testChangePassActionGetView()
    {
        parent::authMock();
        $this->mockService();
        $this->dispatch('/reference/user/changepass', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('changepass');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/changepass');
    }

    public function testChangePassActionWithInvalidForm()
    {
        parent::authMock();
        $this->mockService();
        $this->dispatch('/reference/user/changepass', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertActionName('changepass');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/changepass');
    }

    public function testChangePassActionWithForm()
    {
        parent::authMock();
        $this->mockService();
        $this->mockForm();
        $this->dispatch('/reference/user/changepass', 'POST');
        $this->assertResponseStatusCode(302);
        $this->assertActionName('changepass');
        $this->assertStandardParameters();
        $this->assertMatchedRouteName('user/changepass');
    }

    private function mockForm($isValid = true)
    {
        $mockForm = $this->getMockBuilder(UserForm::class)->disableOriginalConstructor()->getMock();
        $mockForm->expects($this->any())->method('get')->willReturn(new Element());
        $mockForm->expects($this->any())->method('isValid')->willReturn($isValid);

        $this->overrideService(UserForm::class, $mockForm);
    }

    private function mockService($findAllReturn = [])
    {
        $mockEntity = new User();
        $mockEntity->setName('testName');
        $mockEntity->setPass('123');
        $mockEntity->setPassword('123');

        $mockService = $this->getMockBuilder(UserService::class)->disableOriginalConstructor()->getMock();
        $mockService->expects($this->any())->method('find')->willReturn($mockEntity);
        $mockService->expects($this->any())->method('findAll')->willReturn($findAllReturn);
        $mockService->expects($this->any())->method('getEntity')->willReturn(User::class);

        $this->overrideService(UserService::class, $mockService);
    }

    private function assertStandardParameters()
    {
        $this->assertModuleName('reference');
        $this->assertControllerName(UserController::class);
        $this->assertControllerClass('UserController');
    }

    protected function authMock()
    {
        switch ($this->getName()) {
            case "testLoginActionIfAuthenticationIsFailed":
                $this->loginResult = Result::FAILURE;
                break;
            default:
                $this->loginResult = Result::SUCCESS;
        }

        $mockAuth = $this->getMockBuilder(AuthenticationService::class)->disableOriginalConstructor()->getMock();
        $mockAuth->expects($this->any())->method('getIdentity')->willReturn(null);

        $adapter = new ObjectRepository();
        $result = new Result($this->loginResult, null);
        $mockAuth->expects($this->any())->method('getAdapter')->willReturn($adapter);
        $mockAuth->expects($this->any())->method('authenticate')->willReturn($result);


        $this->getApplicationServiceLocator()->setAllowOverride(true);
        $this->getApplicationServiceLocator()->setService('authenticationService', $mockAuth);
        $this->getApplicationServiceLocator()->setAllowOverride(false);

        //$session->check = md5('127.0.0.1ie');
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'ie';
    }
}
