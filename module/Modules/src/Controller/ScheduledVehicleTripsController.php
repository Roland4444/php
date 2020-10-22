<?php
namespace Modules\Controller;

use Application\Exception\ServiceException;
use Modules\Entity\MoveVehiclesEntity;
use Modules\Form\MoveVehiclesCompleteForm;
use Modules\Form\MoveVehiclesForm;
use Modules\Service\CompletedVehicleTripsService;
use Reference\Service\VehicleService;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;

class ScheduledVehicleTripsController extends MoveVehiclesController
{
    protected string $indexRoute = 'scheduledVehicleTrips';

    /**
     * @var bool Определяет какую форму вернуть при вызове родительского метода редактирования.
     */
    protected $isMoveVehiclesCompleteForm = false;

    protected function getCreateForm()
    {
        return new MoveVehiclesForm($this->entityManager);
    }

    /**
     * Возращает сущность для создания нового выезда
     *
     * @param $data
     * @return MoveVehiclesEntity
     */
    protected function getEntityForCreate(array $data)
    {
        $vehicleId = $data['vehicle'];
        $vehicle = $this->services[VehicleService::class]->find($vehicleId);
        $entity = new MoveVehiclesEntity();
        $entity->setCompleted(false);
        $entity->setMoneyDepartment($vehicle->getDepartment());
        return $entity;
    }

    /**
     * @return Form
     */
    protected function getEditForm()
    {
        if ($this->isMoveVehiclesCompleteForm) {
            return new MoveVehiclesCompleteForm($this->entityManager);
        }
        return new MoveVehiclesForm($this->entityManager);
    }

    /**
     * Дополнительная проверка данных
     *
     * @param array $data
     * @return array
     * @throws ServiceException|\Exception
     */
    protected function checkDataForCreate(array $data)
    {
        if (! $this->accessValidate($data['date'], self::LIMIT_MONTH)) {
            throw new ServiceException('Указанная дата не доступна');
        }
        return $data;
    }

    /**
     * Завершение запланированного выезда
     *
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function completeAction()
    {
        $this->isMoveVehiclesCompleteForm = true;
        $this->service = $this->services[CompletedVehicleTripsService::class];
        return $this->editAction();
    }

    protected function getEntityForEdit($id)
    {
        $entity = $this->service->find($id);
        if (! empty($entity) && $this->isMoveVehiclesCompleteForm) {
            $entity->disableRequiredParams();
            $entity->setCompleted(1);
        }
        return $entity;
    }

    protected function checkDataForEdit(array $data)
    {
        if ($this->isMoveVehiclesCompleteForm) {
            $this->indexRoute = 'completedVehicleTrips';
            return $data;
        }

        return parent::checkDataForEdit($data);
    }
}
