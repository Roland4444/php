<?php

namespace Modules\Service;

use Application\Service\BaseService;
use Modules\Entity\WaybillSettings;

class WaybillSettingsService extends BaseService
{
    /**
     * Возвращает данные настроек
     *
     * @return array
     */
    public function getAllSettings()
    {
        return $this->em
            ->getRepository(WaybillSettings::class)
            ->findBy([], $this->order);
    }

    /**
     * @param $settings
     *
     * @return bool
     * @throws \Exception
     */
    public function updateSettings($settings)
    {
        $this->em->getConnection()->beginTransaction();
        try {
            foreach ($settings as $name => $value) {
                $qb = $this->em->createQueryBuilder();
                $qb->update(WaybillSettings::class, 'r')
                    ->set('r.value', ':value')
                    ->where('r.name = :name')
                    ->setParameter('name', $name)
                    ->setParameter('value', $value)
                    ->getQuery()->execute();
            }
            $this->em->flush();
            $this->em->getConnection()->commit();
            return true;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }
}
