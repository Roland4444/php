<?php
namespace Reference\Service;

use Core\Service\AbstractService;
use Reference\Entity\Vehicle;
use Reference\Repositories\VehicleRepository;

/**
 * Class VehicleService
 * @package Reference\Service
 * @method  VehicleRepository getRepository()
 */
class VehicleService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function save($row, $request = null)
    {
        $this->getRepository()->save($row, $request);
    }

    /**
     * Поиск данных с учетом фильтра
     *
     * @param array $params
     * @return array
     */
    public function getTableList(array $params): array
    {
        return $this->getRepository()->getTableList($params);
    }

    /**
     * Возвращает данные для выпадающего списка
     *
     * @return false|string
     */
    public function getVehicleJson()
    {
        $vehicles = $this->getRepository()->findNotArchival();

        $jsonVehicles = [];

        if (! empty($vehicles)) {
            $jsonVehicles[0] = [
                'value' => '',
                'text' => 'Выбрать технику'
            ];
            /** @var Vehicle $vehicle */
            foreach ($vehicles as $vehicle) {
                $jsonVehicles[$vehicle->getId()] = [
                    'value' => $vehicle->getId(),
                    'text' => $vehicle->getName()
                ];
            }
        }
        return json_encode($jsonVehicles);
    }
}
