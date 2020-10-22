<?php
namespace Application\View\Helper;

use Core\Service\DateService;
use Zend\View\Helper\AbstractHelper;

class IsNormalLimitMonth extends AbstractHelper
{
    /**
     * @param $date
     * @param $limit
     * @return bool
     * @throws \Exception
     */
    public function __invoke($date, $limit)
    {
        return DateService::isNormalLimitMonth($date, $limit);
    }
}
