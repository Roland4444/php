<?php
namespace Spare\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Reference\Entity\Warehouse;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\InventoryRepository")
 * @ORM\Table(name="spare_inventory")
 */
class Inventory implements AbstractEntity, InputFilterAwareInterface
{

    const STATUS_SHORTAGE = 'shortage';
    const STATUS_NO_SHORTAGE = 'noShortage';

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $date;

    private $status = '';

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

    /** @ORM\OneToMany(targetEntity="InventoryItems", mappedBy="inventory", cascade={"persist", "remove"},
     *      orphanRemoval=true, fetch="EAGER") */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

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
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        if (! empty($this->status)) {
            return $this->status;
        }

        $this->status = self::STATUS_NO_SHORTAGE;

        foreach ($this->items as $item) {
            /**@var InventoryItems $item*/
            if ($item->getQuantityFact() < $item->getQuantityFormal()) {
                $this->status = self::STATUS_SHORTAGE;
                break;
            }
        }

        return $this->status;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $key
     */
    public function removeItem($key)
    {
        $this->items->remove($key);
    }

    /**
     * @param $item
     */
    public function addItem($item)
    {
        $this->items->add($item);
    }

    /**
     * @param $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }

    /**
     * @param mixed Warehouse
     */
    public function setWarehouse(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
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

    public function toJson()
    {
        $result = [
            'date' => $this->getDate(),
            'inventoryId' => $this->getId(),
        ];

        foreach ($this->getItems() as $item) {
            $result['positions'][$item->getSpare()->getId()] = [
                'spareId' => $item->getSpare()->getId(),
                'spareName' => $item->getSpare()->getName(),
                'totalFact' => $item->getQuantityFact(),
                'totalFormal' => $item->getQuantityFormal(),
            ];
        }
        return json_encode($result);
    }
}
