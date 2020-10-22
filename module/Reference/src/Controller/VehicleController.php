<?php
namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Application\Form\Filter\NameElement;
use Application\Form\Filter\SubmitElement;
use Application\Form\Filter\VehicleTypeElement;
use Reference\Form\VehicleForm;
use Reference\Service\VehicleService;

class VehicleController extends AbstractReferenceController
{
    use FilterableController;
    /**
     * VehicleController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, VehicleService::class, VehicleForm::class);
        $this->routeIndex = "tech";
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
        return new SubmitElement(new VehicleTypeElement(new NameElement($this->entityManager)));
    }
}
