<?php
namespace Modules\Controller;

use Application\Controller\Plugin\CurrentUser;
use Application\Exception\ServiceException;
use Application\Form\Filter\CustomerTextElement;
use Application\Form\Filter\DepartmentElement;
use Application\Form\Filter\FilterableController;
use Application\Form\Filter\VehicleElement;
use Core\Controller\CrudController;
use Core\Service\DateService;
use Application\Form\Filter\DateElement;
use Application\Form\Filter\SubmitElement;
use Exception;
use Modules\Entity\MoveVehiclesEntity;

/**
 * Class MoveVehiclesController
 * @package Modules\Controller
 * @method CurrentUser currentUser()
 */
abstract class MoveVehiclesController extends CrudController
{
    use FilterableController;

    const LIMIT_MONTH = 2;

    /**
     * Получение данных для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    protected function getTableListData($params)
    {
        return $this->service->findBy($params);
    }

    protected function getCreateForm()
    {
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        $entityManager = $this->entityManager;

        return new SubmitElement(
            new VehicleElement(
                new DepartmentElement(new CustomerTextElement(new DateElement($entityManager)), $this->currentUser()->isAdmin())
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
            'complete' => $this->hasAccess(static::class, 'complete'),
        ];
    }

    /**
     * Определяет есть ли возможность устанавливать дату вне диапазона
     *
     * @param string  $date
     * @param integer $limit
     *
     * @return bool
     * @throws Exception
     */
    protected function accessValidate(string $date, $limit)
    {
        return $this->hasAccess(static::class, 'delete') || DateService::isNormalLimitMonth($date, $limit);
    }

    /**
     * Проверка прав на изменение данных
     *
     * @param MoveVehiclesEntity $entity
     *
     * @return bool
     * @throws Exception
     */
    protected function checkAccessToEdit($entity)
    {
        return $this->accessValidate($entity->getDate(), self::LIMIT_MONTH);
    }

    /**
     * Проверка данных при редактировании
     *
     * @param array $data
     * @return array
     * @throws ServiceException
     * @throws Exception
     */
    protected function checkDataForEdit(array $data)
    {
        if (! $this->accessValidate($data['date'], self::LIMIT_MONTH)) {
            throw new ServiceException('Указанная дата не доступна');
        }
        return $data;
    }
}
