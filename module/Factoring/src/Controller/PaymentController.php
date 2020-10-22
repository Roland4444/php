<?php

namespace Factoring\Controller;

use Application\Form\Filter\DateElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\SubmitElement;
use Application\Form\Filter\TraderElement;
use Core\Controller\CrudController;
use Core\Traits\RestMethods;
use Factoring\Entity\Payment;
use Factoring\Service\PaymentService;

/**
 * Class PaymentController
 * @package Factoring\Controller
 * @property PaymentService $service
 */
class PaymentController extends CrudController
{
    use FilterableController;
    use RestMethods;

    protected string $indexRoute = 'factoring_payments';

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
        $items = $this->service->findByPeriod($params['startdate'], $params['enddate'], $params['trader']);
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
        return new SubmitElement(new TraderElement(new DateElement($this->entityManager)));
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
        $row->setBank($this->services['bankService']->findDefault());
        $form->bind($row);
        return $form;
    }

    /**
     * {@inheritDoc}
     */
    protected function getEntityForCreate(array $data)
    {
        $row = new Payment();
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
            'confirm' => $this->hasAccess(static::class, 'confirm'),
        ];
    }
}
