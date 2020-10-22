<?php

namespace Reference\Service;

use Application\Service\BaseService;
use Reference\Entity\ShipmentTariff;

/**
 * Class ShipmentTariffService
 *
 * @package Reference\Service
 */
class ShipmentTariffService extends BaseService
{
    protected $order = ['name' => 'ASC'];

    /**
     * ShipmentTariffService constructor.
     */
    public function __construct()
    {
        $this->setEntity(ShipmentTariff::class);
    }

    /**
     * {@inheritdoc}
     */
    public function save($row, $request = null)
    {
        if ($row->getDef()) {
            $this->clearDef();
        }
        $this->em->persist($row);
        $this->em->flush();
    }

    private function clearDef()
    {
        $query = $this->em->createQuery(
            "UPDATE " . $this->entity . " s SET s.def = '0'"
        );
        $query->execute();
    }
}
