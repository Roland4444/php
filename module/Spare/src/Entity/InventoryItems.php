<?php
namespace Spare\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\InventoryItemsRepository")
 * @ORM\Table(name="spare_inventory_items")
 */
class InventoryItems implements InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\Spare", fetch="EAGER")
     * @ORM\JoinColumn(name="spare_id", referencedColumnName="id")
     */
    private $spare;

    /**
     * @ORM\ManyToOne(targetEntity="Inventory", inversedBy="items", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="inventory_id", referencedColumnName="id")
     */
    private $inventory;

    /** @ORM\Column(name="quantity_formal", type="decimal") */
    private $quantityFormal;

    /** @ORM\Column(name="quantity_fact", type="decimal") */
    private $quantityFact;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Spare
     */
    public function getSpare()
    {
        return $this->spare;
    }

    /**
     * @param mixed $spare
     */
    public function setSpare($spare)
    {
        $this->spare = $spare;
    }

    /**
     * @return Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param mixed $inventory
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * @return mixed
     */
    public function getQuantityFormal()
    {
        return $this->quantityFormal;
    }

    /**
     * @param mixed $quantityFormal
     */
    public function setQuantityFormal($quantityFormal)
    {
        $this->quantityFormal = $quantityFormal;
    }

    /**
     * @return mixed
     */
    public function getQuantityFact()
    {
        return $this->quantityFact;
    }

    /**
     * @param mixed $quantityFact
     */
    public function setQuantityFact($quantityFact)
    {
        $this->quantityFact = $quantityFact;
    }


    /**
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'spare',
                'required' => true,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
