<?php

namespace OfficeCash\Controller;

use Core\Traits\RestMethods;
use InvalidArgumentException;
use JsonException;
use LogicException;
use OfficeCash\Entity\TransportIncome;
use OfficeCash\Service\TransportIncomeService;
use Zend\Http\Response;
use Zend\I18n\View\Helper\NumberFormat;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\Regex;
use Zend\View\Model\ViewModel;

/**
 * Class TransportIncomeController
 * @package OfficeCash\Controller
 * @method bool hasAccess(string $className, string $permission)
 */
class TransportIncomeController extends AbstractActionController
{
    use RestMethods;

    private TransportIncomeService $service;

    public function __construct(TransportIncomeService $service)
    {
        $this->service = $service;
    }

    public function indexAction()
    {
        return new ViewModel([
            'access' => json_encode($this->getPermissions()),
        ]);
    }

    public function getAction(): Response
    {
        $id = $this->getRequest()->getPost('id');
        try {
            $row = $this->service->find($id);
            return $this->responseSuccess(['data' => $row]);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
    }

    /**
     * @return Response
     * @throws JsonException
     */
    public function listAction(): Response
    {
        $dateFrom = $this->params()->fromPost('startdate');
        $dateTo = $this->params()->fromPost('enddate');
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
            $id = (int)$this->params()->fromPost('id');
            if ($id > 0 && ! $this->getPermissions()['edit']) {
                throw new LogicException('Access denied');
            }
            $date = $this->getRequest()->getPost('date');
            $money = $this->getRequest()->getPost('money');
            $dateValidator = new Date();
            $moneyValidator = new Regex(['pattern' => '/^[0-9]+((\.|,)[0-9]*)?$/']);

            if (! $dateValidator->isValid($date) || ! $moneyValidator->isValid($money)) {
                throw new InvalidArgumentException('Введены не корретные данные');
            }
            if (! $this->getPermissions()['delete'] && $date !== date('Y-m-d')) {
                throw new InvalidArgumentException('Вы можете добавлять данные только с текущей датой');
            }
            $income = $id > 0 ? $this->service->find($id) : new TransportIncome();
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
        $id = $this->params()->fromPost('id');
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
