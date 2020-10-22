<?php

namespace Factoring\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Core\Controller\CrudController;
use Factoring\Entity\AssignmentDebt;

class AssignmentDebtController extends CrudController
{
    use FilterableController;

    protected string $indexRoute = 'factoring_assignment_debt';

    /**
     * Получить данные для индексной страницы
     *
     * @param $params
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getTableListData($params)
    {
        $items = $this->service->findByPeriod($params['startdate'], $params['enddate']);
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item->getMoney();
        }
        return [
            'items' => $items,
            'sum' => $sum,
        ];
    }

    /**
     * Filter form
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new DateElement());
    }

    /**
     * Create form
     *
     * @return mixed
     */
    protected function getCreateForm()
    {
        $form = $this->services['form'];
        $row = $this->getEntityForCreate([]);
        $form->bind($row);
        return $form;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEntityForCreate(array $data)
    {
        $row = new AssignmentDebt();
        $row->setDate(date('Y-m-d'));
        return $row;
    }

    /**
     * Edit form
     *
     * @return mixed
     */
    protected function getEditForm()
    {
        return $this->services['form'];
    }

    /**
     * Get access to see components
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }
}
