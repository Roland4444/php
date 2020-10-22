<?php
namespace Reference\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="trader")
 */
class Trader implements \JsonSerializable
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="boolean") */
    private $def;

    /** @ORM\Column(type="string") */
    private $inn;

    /**
     * @ORM\ManyToOne(targetEntity="TraderParent", inversedBy="traders",cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

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

    public function setInn($inn)
    {
        $this->inn = $inn;
    }
    public function getInn()
    {
        return $this->inn;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }
    public function getParent()
    {
        return $this->parent;
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
            'inn' => $this->getInn(),
            'def' => $this->getDef(),
            'text' => $this->getName(),
            'value' => $this->getId(),
        ];
    }
}
