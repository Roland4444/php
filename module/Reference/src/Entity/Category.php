<?php

namespace Reference\Entity;

use Core\Entity\EntityWithOptions;
use Core\Utils\Options;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="\Reference\Repositories\CategoryRepository")
 * @ORM\Table(name="cost_category")
 */
class Category implements JsonSerializable
{
    use EntityWithOptions;

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="CategoryGroup")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;

    /** @ORM\Column(name="options",type="json") */
    private $options;

    /**
     * @ORM\ManyToMany(targetEntity="Reference\Entity\Role")
     * @ORM\JoinTable(name="category_role_ref",
     *      joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

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
        return mb_strtoupper($this->name);
    }

    public function setGroup($group)
    {
        $this->group = $group;
        $group->addCategory($this);
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options): void
    {
        $this->options = $options;
    }

    public function isArchived()
    {
        return $this->hasOption(Options::ARCHIVAL);
    }

    public function isDefault()
    {
        return $this->hasOption(Options::DEFAULT);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'def' => $this->isDefault(),
            'value' => $this->getId(),
            'text' => $this->getName()
        ];
    }
}
