<?php

namespace Core\Service;

use Reference\Entity\User;
use Zend\Authentication\AuthenticationService;
use Zend\Cache\Exception\LogicException;

class AccessValidateService
{
    private AuthenticationService $authService;
    private DateService $dateService;

    public function __construct($authService, $dateService)
    {
        $this->authService = $authService;
        $this->dateService = $dateService;
    }

    /**
     * @param int $depthDays
     * @param string $date
     * @param $row
     * @throws LogicException
     */
    public function validateAccessForDays(int $depthDays, string $date, $row = null): void
    {
        if (! $this->getAuthUser()->isAdmin() || $this->getAuthUser()->isGlavbuh()) {
            if ($row && $row->getId()) {
                $rowDate = date_create($row->getDate());
                if (! $this->dateService->checkDateInRangeDays($rowDate, $depthDays)) {
                    throw new LogicException('Запрещено редактирование записей старше ' . $depthDays . ' дней.');
                }
            }
            $newDate = date_create($date);
            if (! $this->dateService->checkDateInRangeDays($newDate, $depthDays)) {
                throw new LogicException('Выберите дату не раньше ' . $depthDays . ' дней от текущей.');
            }
        }
    }

    /**
     * @return User
     */
    private function getAuthUser(): User
    {
        return $this->authService->getIdentity();
    }
}
