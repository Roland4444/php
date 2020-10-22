<?php

namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Application\Form\Filter\NameElement;
use Application\Form\Filter\SubmitElement;
use Storage\Plugin\CurrentDepartment;
use Zend\Json\Json;
use Reference\Form\CustomerForm;
use Reference\Service\CustomerService;

/**
 * Поставщики лома
 * Class CustomerController
 *
 * @package Reference\Controller
 * @method CurrentDepartment currentDepartment()
 */
class CustomerController extends AbstractReferenceController
{
    use FilterableController;

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * CustomerController constructor.
     *
     * @param \Zend\ServiceManager\ServiceManager $container
     */
    public function __construct($container)
    {
        parent::__construct($container, CustomerService::class, CustomerForm::class);
        $this->routeIndex = "customer";
        $this->authService = $container->get('authenticationService');
    }

    /**
     * {@inheritdoc}
     */
    public function findRowsForIndex(?array $params)
    {
        return $this->getService()->getTableList($params);
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new NameElement($this->entityManager));
    }

    /**
     * Получить список поставщиков
     * @return mixed
     */
    public function listAction()
    {
        if ($this->getRequest()->isPost()) {
            $customers = $this->service->getCustomers();

            return $this->getResponse()->setContent(Json::encode($customers));
        }
        exit();
    }

    /**
     * Получить список поставщиков, сдававших лом в последнее время
     * @return mixed
     */
    public function listUsedAction()
    {
        if ($this->getRequest()->isPost()) {
            $departmentId = (int)$this->getRequest()->getPost('department');
            $customers = $this->service->findUsed($departmentId);
            return $this->getResponse()->setContent(Json::encode($customers));
        }
        exit();
    }
}
