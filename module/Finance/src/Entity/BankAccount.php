<?php

namespace Finance\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class BankAccount
 * @ORM\Entity(repositoryClass="\Finance\Repositories\BankAccountRepository")
 * @ORM\Table(name="bank_account")
 */
class BankAccount implements \JsonSerializable, AbstractEntity
{
    const ALIAS_RNKO = 'rnko';

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="string") */
    private $bank;

    /** @ORM\Column(type="string") */
    private $alias;

    /** @ORM\Column(type="boolean") */
    private $cash;

    /** @ORM\Column(type="boolean") */
    private $def;

    /** @ORM\Column(type="boolean") */
    private $closed;

    /** @ORM\Column(type="boolean") */
    private $diamond;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getShortName()
    {
        if (is_numeric($this->getName())) {
            return substr($this->getName(), strlen($this->getName()) - 3, strlen($this->getName()));
        }
        return $this->getName();
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function setCash($cash)
    {
        $this->cash = $cash;
    }

    public function getCash()
    {
        return $this->cash;
    }

    public function setDef($def)
    {
        $this->def = $def;
    }

    public function getDef()
    {
        return $this->def;
    }

    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * @return mixed
     */
    public function getDiamond()
    {
        return $this->diamond;
    }

    /**
     * @param mixed $diamond
     */
    public function setDiamond($diamond): void
    {
        $this->diamond = $diamond;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'short' => $this->getShortName(),
            'bank' => $this->getBank(),
            'text' => $this->getName(),
            'value' => $this->getId(),
            'def' => $this->getDef(),
        ];
    }
}
