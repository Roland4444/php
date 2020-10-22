<?php

namespace Reference\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Class Metal
 * @ORM\Entity(repositoryClass="\Reference\Repositories\MetalRepository")
 * @ORM\Table(name="metal")
 */
class Metal implements AbstractEntity, JsonSerializable
{
    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="integer") */
    private $def;

    /**
     * @ORM\ManyToOne(targetEntity="MetalGroup", inversedBy="metals",cascade={"persist"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;

    /** @ORM\Column(type="string") */
    private $code;

    /** @ORM\Column(type="string") */
    private $alias;

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

    public function setDef($def)
    {
        $this->def = $def;
    }

    public function getDef()
    {
        return $this->def;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setGroup($group)
    {
        $this->group = $group;
        $group->addMetal($this);
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias): void
    {
        $this->alias = $alias;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'def' => (bool) $this->def,
        ];
    }
}
