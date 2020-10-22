<?php

namespace OfficeCash\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Zend\I18n\View\Helper\NumberFormat;

/**
 * Class TransportIncome
 *
 * @ORM\Entity(repositoryClass="\OfficeCash\Repository\TransportIncomeRepository")
 * @ORM\Table(name="office_cash_transport_income")
 */
class TransportIncome implements \JsonSerializable, AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $date;

    /**
     * @ORM\Column(type="decimal")
     */
    private $money;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @param mixed $money
     */
    public function setMoney($money): void
    {
        $this->money = $money;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $numberFormat = new NumberFormat();
        return [
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'money' => $this->getMoney(),
            'moneyFormat' => $numberFormat($this->getMoney(), \NumberFormatter::DECIMAL, \NumberFormatter::TYPE_DEFAULT, 'ru_RU'),
        ];
    }
}
