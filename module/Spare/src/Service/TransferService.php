<?php

namespace Spare\Service;

use Core\Service\AbstractService;
use Spare\Entity\Transfer;
use Spare\Repositories\TransferRepository;

/**
 * Class TransferService
 * @package Spare\Service
 * @method  TransferRepository getRepository() Метод класса AbstractService
 */
class TransferService extends AbstractService
{
    /**
     * @param $request
     * @param $positions
     * @param $warehouseSourceId
     * @return string
     */
    public function validatePositions($request, $positions, $warehouseSourceId)
    {
        $warehouseId = (int) $request->get('warehouse');
        $msg = '';
        if (empty($warehouseId)) {
            $msg = 'Не передан склад';
        }

        if ($warehouseId == $warehouseSourceId) {
            $msg = 'Склад эспорта не может быть складом импорта';
        }

        $transferId = (int) $request->get('transferId');
        if (! empty($transferId) &&  count($positions) != 1) {
            $msg = 'Неверно переданы параметры';
        }

        return $msg;
    }

    /**
     * @param $positions
     * @return array|string
     */
    public function getData($positions)
    {
        $data = [];
        foreach ($positions as $position) {
            if ((int)$position['count'] < 1) {
                return 'Кол-во не может быть отрицательным значением или быть меньше 1';
            }

            if (empty((int) $position['count']) || empty((int) $position['spare'])) {
                return 'Неверно переданы параметры';
            }
            $spareId = (int)$position['spare'];
            $data[$spareId] = [
                'spareId' => $spareId,
                'count' => (int)$position['count'],
                'comment' => ! empty($position['comment']) ? $position['comment'] : '',
            ];
        }

        return $data;
    }

    /**
     * Сохранение трансферов
     *
     * @param $transfers
     * @throws
     */
    public function saveTransfers(array $transfers)
    {
        if (! empty($transfers)) {
            foreach ($transfers as $transfer) {
                $this->getRepository()->save($transfer);
            }
        }
    }

    /**
     * Поиск расходов для индексной страницы
     *
     * @param $params
     * @return mixed
     */
    public function findTransfers($params)
    {
        return $this->getRepository()->findTransfers($params);
    }

    /**
     * Преобразование данных для фронта
     *
     * @param Transfer $transfer
     * @return false|string
     */
    public function getDataFromTransfer(Transfer $transfer)
    {
        $transfers = [
            'transferId' => $transfer->getId(),
            'date' => $transfer->getDate(),
            'warehouse' => $transfer->getDest()->getId(),
        ];

        $transfers['data'][] = [
            'spare' => $transfer->getSpare()->getId(),
            'count' => $transfer->getQuantity(),
        ];

        return json_encode($transfers, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Возвращает остатки по переброскам для указанного склада
     *
     * @param $warehouseId
     * @param $dateStart
     * @return array
     */
    public function getTotalTransfers($warehouseId, $dateStart = '')
    {
        $transfers = $this->getRepository()->getTotalTransfers($warehouseId, $dateStart);

        if (empty($transfers)) {
            return [];
        }

        $result = [];
        foreach ($transfers as $transfer) {
            $spareId = $transfer['spare_id'];

            $quantity = 0;
            if ($transfer['source'] == $warehouseId) {
                $quantity -= $transfer['sum'];
            } else {
                $quantity += $transfer['sum'];
            }

            if (! isset($result[$spareId])) {
                $result[$spareId] = [
                    'spare_id' => $spareId,
                    'spareUnits' => $transfer['spare_units'],
                    'total' => $quantity,
                    'text' => $transfer['spare_name'],
                ];
            } else {
                $result[$spareId]['total'] += $quantity;
            }
        }
        return $result;
    }

    /**
     * Получить перемещения на заданную дату
     * @param int $warehouseId
     * @param $date
     * @return mixed
     */
    public function getTransfersByDate(int $warehouseId, $date)
    {
        return $this->getRepository()->findBy([
           'date' => $date,
           'source' => $warehouseId
        ]);
    }
}
