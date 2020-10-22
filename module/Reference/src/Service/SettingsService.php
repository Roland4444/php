<?php

namespace Reference\Service;

use Core\Service\AbstractService;
use Reference\Entity\Settings;

/**
 * Class DepartmentService
 *
 * @package Reference\Service
 */
class SettingsService extends AbstractService
{
    protected array $order = ['id' => 'ASC'];

    /**
     * Поиск значения настройки по алеасу
     *
     * @param $alias
     * @return object|null
     */
    public function findByAlias($alias): ?Settings
    {
        return $this->getRepository()->findOneBy(['alias' => $alias]);
    }
}
