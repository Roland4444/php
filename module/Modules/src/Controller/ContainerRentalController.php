<?php

namespace Modules\Controller;

use Application\Form\Filter\FilterableController;
use Modules\Form\Payment;
use Modules\Service\ContainerRentalService;
use Reference\Service\ContainerOwnerService;
use Storage\Facade\Storage;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\OwnerElement;
use Application\Form\Filter\SubmitElement;

//todo надо выделить пользование в отдельный модуль или удалить нафиг
class ContainerRentalController extends AbstractActionController
{
    use FilterableController;

    protected $serviceLocator;
    private Storage $storage;

    /** @var ContainerRentalService */
    private $rentalService;


    public function __construct($container)
    {
        $this->storage = $container->get('storage');
        $this->rentalService = $container->get(ContainerRentalService::class);
        $this->serviceLocator = $container;
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        $entityManager = $this->serviceLocator->get('entityManager');
        return new SubmitElement(new OwnerElement(new DateElement($entityManager)));
    }

    public function indexAction()
    {
        $filterForm = $this->filterForm($this->getRequest(), 'containerRental');
        $params = $filterForm->getFilterParams('containerRental');

        $ownerService = $this->serviceLocator->get(ContainerOwnerService::class);
        $companies = $ownerService->findAll();

        $total = $this->rentalService->getTotalByCompanies($companies, $params['startdate'], $params['enddate']);

        $containersData = $this->storage->findContainersByOwnerAndDates($params['owner'], $params['startdate'], $params['enddate']);
        $expenses_sum = $this->storage->getContainersSumByOwnerFormalOrdered($params['owner'], $params['startdate'], $params['enddate']);

        return new ViewModel([
            'total' => $total,
            'form' => $filterForm->getForm($params),
            'containers' => $containersData,
            'exp_sum' => $expenses_sum,
        ]);
    }

    public function editAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id', null);
        $ownerData = $this->storage->getExtraOwnerDataByContainerId($id);
        $arr = new \ArrayObject();
        $arr['date_formal'] = $ownerData['dateFormalString'];
        $arr['money'] = $ownerData['ownerCost'];
        $arr['is_paid'] = $ownerData['isPaid'];
        $form = new Payment();
        $form->bind($arr);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $postData = (array)$this->getRequest()->getPost();
                $this->storage->saveContainerExtraOwner($id, $postData);
                return $this->redirect()->toRoute('containerRental');
            }
        }

        return new ViewModel([
            'form' => $form,
            'id' => $id
        ]);
    }
}
