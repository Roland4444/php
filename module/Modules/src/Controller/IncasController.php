<?php

namespace Modules\Controller;

use Core\Traits\RestMethods;
use Modules\Entity\TransportIncas;
use Modules\Service\TransportIncasService;
use Zend\Http\Response;
use Zend\I18n\View\Helper\NumberFormat;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\Regex;
use Zend\View\Model\ViewModel;

class IncasController extends AbstractActionController
{
    use RestMethods;

    private TransportIncasService $service;

    public function __construct(TransportIncasService $service)
    {
        $this->service = $service;
    }

    public function indexAction()
    {
        return new ViewModel([
            'access' => json_encode($this->getPermissions()),
        ]);
    }

    public function listAction()
    {
        $dateFrom = $this->getRequest()->getPost('startdate');
        $dateTo = $this->getRequest()->getPost('enddate');
        if (! is_numeric(strtotime($dateFrom)) || ! is_numeric(strtotime($dateTo))) {
            return $this->responseError('Не корректно указаны даты');
        }
        $data = $this->service->getByPeriod($dateFrom, $dateTo);
        $moneySum = 0;
        foreach ($data as $row) {
            $moneySum += $row->getMoney();
        }

        $numberFormat = new NumberFormat();
        return $this->responseSuccess([
            'rows' => $data,
            'moneySum' => $numberFormat($moneySum, \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, 'ru_RU'),
        ]);
    }

    public function saveAction(): Response
    {
        try {
            $id = (int)$this->getRequest()->getPost('id');
            if ($id > 0 && ! $this->getPermissions()['edit']) {
                throw new \Exception('Access denied');
            }
            $date = $this->getRequest()->getPost('date');
            $money = $this->getRequest()->getPost('money');
            $dateValidator = new Date();
            $moneyValidator = new Regex(['pattern' => '/^[0-9]+((\.|,)[0-9]*)?$/']);

            if (! $dateValidator->isValid($date) || ! $moneyValidator->isValid($money)) {
                throw new \Exception('Введены не корретные данные');
            }
            if (! $this->getPermissions()['delete'] && $date !== date('Y-m-d')) {
                throw new \Exception('Вы можете добавлять данные только с текущей датой');
            }
            $income = $id > 0 ? $this->service->find($id) : new TransportIncas();
            $income->setDate($date);
            $income->setMoney($money);
            $this->service->save($income);
            return $this->responseSuccess();
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    public function deleteAction(): Response
    {
        $id = $this->getRequest()->getPost('id');
        try {
            $this->service->remove($id);
            return $this->responseSuccess();
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    protected function getPermissions(): array
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }
}
