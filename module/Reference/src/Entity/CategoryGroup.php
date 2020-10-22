<?php
namespace Reference\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cost_category_group")
 */
class CategoryGroup
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="integer") */
    private $sortOrder;

    /**
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="group",cascade={"persist","remove"}))
     */
    private $categories;

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

    /**
     * @return mixed
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param mixed $sortOrder
     */
    public function setSortOrder($sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    public function addCategory($category)
    {
        $this->categories[] = $category;
    }

    public function getCategories()
    {
        return $this->categories;
    }
}
