<?php
namespace Reference\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MetalGroup
 * @ORM\Entity
 * @ORM\Table(name="metal_group")
 */
class MetalGroup
{

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="integer") */
    private $ferrous;

    /**
     *
     * @ORM\OneToMany(targetEntity="Metal", mappedBy="group",cascade={"persist","remove"}))
     */
    private $metals;

    public function addMetal($metal)
    {
        $this->metals[] = $metal;
    }

    public function getMetals()
    {
        return $this->metals;
    }

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

    public function setFerrous($ferrous)
    {
        $this->ferrous = $ferrous;
    }

    public function getFerrous()
    {
        return $this->ferrous;
    }
}
