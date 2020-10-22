<?php
namespace Reference\Controller;

use Application\Form\Filter\FilterableController;
use Application\Form\Filter\INNElement;
use Application\Form\Filter\NameElement;
use Application\Form\Filter\SubmitElement;
use Application\Form\Filter\TraderParentElement;
use Reference\Form\TraderForm;
use Reference\Service\TraderService;

/**
 * Class TraderController
 * @package Reference\Controller
 */
class TraderController extends AbstractReferenceController
{
    use FilterableController;

    /**
     * @var array Для проверки уникального значения
     */
    protected $propertyForExistValidate = ['name', 'inn'];

    /**
     * TraderController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, TraderService::class, TraderForm::class);
        $this->routeIndex = "trader";
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
        return new SubmitElement(new INNElement(new TraderParentElement(new NameElement($this->entityManager))));
    }
}
