<?php
namespace Spare\Controller;

use Application\Controller\Plugin\CurrentUser;
use Core\Service\AccessValidateService;
use Core\Traits\RestMethods;
use Exception;
use Reference\Entity\Warehouse;
use Zend\Mvc\MvcEvent;

/**
 * Class SpecifiedSpareController
 * @package Spare\Controller
 * @method CurrentUser currentUser()
 */
abstract class SpecifiedSpareController extends BaseSpareController
{
    use RestMethods;

    protected $routeParams;
    protected $services;
    protected $entityManager;
    protected AccessValidateService $accessValidateService;
    protected int $depth = 14;

    /**
     * BaseSpareController constructor.
     * @param $entityManager
     * @param $services
     * @param $accessValidateService
     */
    public function __construct($entityManager, $services, $accessValidateService)
    {
        $this->entityManager = $entityManager;
        $this->accessValidateService = $accessValidateService;
        $this->services = $services;
        $this->inventoryService = $services['inventoryService'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function onDispatch(MvcEvent $e)
    {
        if (! $this->hasAccessForWarehouseId()) {
            exit();
        }
        $this->routeParams = [
            'warehouse' => $this->getWarehouseId()
        ];
        return parent::onDispatch($e);
    }

    private function hasAccessForWarehouseId()
    {
        $warehouses = $this->currentUser()->getWarehouses();

        if (! count($warehouses)) {
            return false;
        }

        /** @var Warehouse $warehouse */
        foreach ($warehouses as $warehouse) {
            if ($warehouse->isClosed()) {
                continue;
            }
            if ($warehouse->getId() == $this->getWarehouseId()) {
                return true;
            }
        }
        return false;
    }

    protected function getWarehouseId()
    {
        $warehouseId = $this->getEvent()->getRouteMatch()->getParam('warehouse', null);

        /** @var Warehouse $uWarehouse */
        foreach ($this->currentUser()->getWarehouses() as $uWarehouse) {
            if ($uWarehouse->getId() == $warehouseId) {
                return $warehouseId;
            }
        }

        throw new Exception('Склад не назначен данному пользователю');
    }
}
