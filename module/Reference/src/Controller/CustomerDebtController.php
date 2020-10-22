<?php
namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Reference\Entity\CustomerDebt;
use Reference\Form\CustomerDebtForm;
use Reference\Service\CustomerDebtService;

/**
 * Class CustomerDebtController
 * @package Reference\Controller
 */
class CustomerDebtController extends AbstractReferenceController
{
    use FilterableController;
    /**
     * SpareController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, CustomerDebtService::class, CustomerDebtForm::class);
        $this->routeIndex = "customer_debt";
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntityForBind($entityName)
    {
        $debt = new CustomerDebt();
        $debt->setDate(date('Y-m-d'));
        return $debt;
    }
}
