<?php

namespace Core\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class DateService
 * @package Core\Service
 */
class DateService implements FactoryInterface
{
    /**
     * Проверяет что дата лежит в диапозоне
     * @param \DateTime $start
     * @param \DateTime $end
     * @param \DateTime $date
     * @return bool
     */
    public function checkDateInRange(\DateTime $start, \DateTime $end, \DateTime $date): bool
    {
        $startTs = $start->getTimestamp();
        $endTs = $end->getTimestamp();
        $userTs = $date->getTimestamp();

        // Check that user date is between start & end
        return (($userTs >= $startTs) && ($userTs <= $endTs));
    }

    /**
     * Проверяем что дата лежит в диапозоне месяца
     * @param \DateTime $date
     * @return bool
     */
    public function checkDateInRangeMonth(\DateTime $date): bool
    {
        $today = date("Y-m-d");
        $start = (new \DateTime($today))->modify('-1 month');
        $end = new \DateTime($today);

        return $this->checkDateInRange($start, $end, $date);
    }

    /**
     * Проверяем что дата лежит в диапозоне двух дней
     * @param \DateTime $date
     * @return bool
     */
    public function checkDateInRangeTwoDays(\DateTime $date): bool
    {
        $today = date("Y-m-d");
        $start = (new \DateTime($today))->modify('-1 day');
        $end = new \DateTime($today);

        return $this->checkDateInRange($start, $end, $date);
    }

    /**
     * Проверяем что дата лежит в диапозоне $days дней
     * @param \DateTime $date
     * @param int $days
     * @return bool
     */
    public function checkDateInRangeDays(\DateTime $date, int $days): bool
    {
        $today = date("Y-m-d");
        $start = (new \DateTime($today))->modify('-'.$days.' day');
        $end = new \DateTime($today);

        return $this->checkDateInRange($start, $end, $date);
    }

    /**
     * Проверяем что дата лежит в диапозоне $limit месяцов
     *
     * @param string  $date
     * @param integer $limit
     * @return bool
     * @throws \Exception
     */
    public static function isNormalLimitMonth($date, $limit): bool
    {
        $inputDate = new \DateTime($date);
        $now = new \DateTime();
        $dateInterval = $now->diff($inputDate);
        return $dateInterval->m < $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new self();
    }
}
