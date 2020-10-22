<?php
namespace Spare\Service;

use Application\Service\BaseService;
use Spare\Entity\Consumption;
use Spare\Entity\Receipt;
use Spare\Entity\ReceiptItems;

/**
 * Class SpareService
 * @package Reference\SpareService
 */
class TotalService extends BaseService
{
    /**@var ConsumptionService*/
    protected $consumptionService;
    /**@var ReceiptService*/
    protected $receiptService;
    /**@var TransferService*/
    protected $transferService;
    /**@var InventoryService*/
    protected $inventoryService;
    /**@var SpareService*/
    protected $spareService;

    /**
     * TotalService constructor.
     * @param array $services
     */
    public function __construct($services)
    {
        $this->consumptionService = $services['consumptionService'];
        $this->receiptService = $services['receiptService'];
        $this->transferService = $services['transferService'];
        $this->inventoryService = $services['inventoryService'];
        $this->spareService = $services['spareService'];
    }

    /**
     * Возвращает остатки по запчастям с учетом приходов, расходов и трансферов
     *
     * @param integer $warehouseId
     * @param integer   $requiredSpareId
     * @return array
     */
    public function getTotals($warehouseId, $requiredSpareId = null)
    {
        //TODO: параметр $requiredSpareId лучше убрать(изменить логику).
        $lastInventory = $this->inventoryService->getTotalsInventory($warehouseId);
        $dateStart = $lastInventory['dateStart'] ?? '';

        $sparesInInventory = [];
        if (! empty($lastInventory) && ! empty($lastInventory['spares'])) {
            $sparesInInventory = $lastInventory['spares'];
        }

        $totalsReceipt = $this->receiptService->getTotalReceipt($warehouseId, $dateStart);
        $totalsConsumption = $this->consumptionService->getTotalConsumption($warehouseId, $dateStart);
        $totalsTransfer = $this->transferService->getTotalTransfers($warehouseId, $dateStart);

        $spareIds = array_merge(
            array_keys($totalsReceipt),
            array_keys($totalsConsumption),
            array_keys($totalsTransfer),
            array_keys($sparesInInventory)
        );
        $spareIds = array_unique($spareIds);

        if (empty($spareIds)) {
            return [];
        }
        $items = [];
        foreach ($spareIds as $spareId) {
            $quantity = 0;
            $spareName = '';
            $units = null;
            if (isset($totalsReceipt[$spareId])) {
                $quantity = $totalsReceipt[$spareId]['total'];
                $spareName = $totalsReceipt[$spareId]['text'];
                $units = $totalsReceipt[$spareId]['spareUnits'];
            }
            if (isset($totalsConsumption[$spareId])) {
                $quantity -= $totalsConsumption[$spareId]['total'];
                $spareName = $totalsConsumption[$spareId]['text'];
                $units = $totalsConsumption[$spareId]['spareUnits'];
            }
            if (isset($totalsTransfer[$spareId])) {
                $quantity += $totalsTransfer[$spareId]['total'];
                $spareName = $totalsTransfer[$spareId]['text'];
                $units = $totalsTransfer[$spareId]['spareUnits'];
            }
            if (isset($sparesInInventory[$spareId])) {
                $quantity += $sparesInInventory[$spareId]['total'];
                $spareName = $sparesInInventory[$spareId]['text'];
                $units = $sparesInInventory[$spareId]['spareUnits'];
            }

            if (empty($quantity) && $spareId != $requiredSpareId) {
                continue;
            }

            $items[$spareId] = [
                'spare_id' => $spareId,
                'spareUnits' => $units,
                'text' => $spareName,
                'total' => $quantity,
            ];
        }

        return $items;
    }

    /**
     * Проверить, проводились ли операции по приходам, расходам и трансферам на сегодня
     * @param int $warehouseId
     * @param string $date
     * @return bool
     */
    public function checkIfInventoryIsAvailable(int $warehouseId, string $date): bool
    {
        $byDateReceipts = $this->receiptService->getReceiptByDate($warehouseId, $date);

        $byDateConsumptions = $this->consumptionService->getConsumptionsByDate($warehouseId, $date);

        $byDateTransfers = $this->transferService->getTransfersByDate($warehouseId, $date);

        return count($byDateReceipts) == 0 and count($byDateConsumptions) == 0 and count($byDateTransfers) == 0;
    }
}
