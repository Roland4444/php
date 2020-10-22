<?php

namespace Spare\Controller;

use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SpareSellerElement;
use Application\Form\Filter\SpareSellerINNElement;
use Application\Form\Filter\SubmitElement;
use Spare\Form\SellerForm;
use Reference\Controller\AbstractReferenceController;

class SellerController extends AbstractReferenceController
{
    use FilterableController;

    /**
     * @var array Для проверки уникального значения
     */
    protected $propertyForExistValidate = ['name', 'inn'];

    /**
     * SpareController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, 'spareSellerService', SellerForm::class);
        $this->routeIndex = "spareSeller";
    }

    /**
     * Список сущностей
     * @return \Zend\View\Model\ViewModel
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
        return new SubmitElement(new SpareSellerElement(new SpareSellerINNElement($this->entityManager)));
    }

    /**
     * Права доступа
     *
     * @return array
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'save' => $this->hasAccess(static::class, 'save'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }
}
