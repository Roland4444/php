<?php
namespace Spare\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\OrderItemsRepository")
 * @ORM\Table(name="spare_order_items")
 */
class OrderItems implements InputFilterAwareInterface, \JsonSerializable
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="items")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\PlanningItems", inversedBy="itemsOrder")
     * @ORM\JoinColumn(name="planning_items_id", referencedColumnName="id", nullable=true)
     */
    private $planningItem;

    /** @ORM\Column(type="decimal") */
    private $quantity;

    /** @ORM\Column(type="decimal", name="sub_quantity") */
    private $subQuantity;

    /** @ORM\Column(type="decimal") */
    private $price;

    /** @ORM\OneToMany(targetEntity="ReceiptItems", mappedBy="orderItem") */
    private $itemsReceipt;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\Spare")
     * @ORM\JoinColumn(name="spare_id", referencedColumnName="id")
     */
    private $spare;

    public function __construct()
    {
        $this->itemsReceipt = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getItemsReceipt()
    {
        return $this->itemsReceipt;
    }

    /**
     * Возвращает количество пришедших запчастей
     *
     * @param boolean $getWithSubQuantity Если false не учитывает количество в коплекте
     * @return int
     */
    public function getReceipted($getWithSubQuantity = true)
    {
        if (empty($this->itemsReceipt->count())) {
            return 0;
        }

        $quantity = 0;
        $isComposite = ! empty($this->subQuantity);
        foreach ($this->itemsReceipt as $itemReceipt) {
            if ($isComposite && $getWithSubQuantity) {
                $quantity += $itemReceipt->getQuantity() * $itemReceipt->getSubQuantity();
            } else {
                $quantity += $itemReceipt->getQuantity();
            }
        }
        return $quantity;
    }

    /**
     * Определяет заказанное количество
     *
     * @return float|int
     */
    public function countInOrder()
    {
        $isComposite = ! empty($this->subQuantity);
        if ($isComposite) {
            return $this->quantity * $this->subQuantity;
        }
        return $this->quantity;
    }

    /**
     * Возвращается подсчитанная цена данной части заказа
     *
     * @return float|int
     */
    public function getOrderItemsPrice()
    {
        return $this->quantity * $this->price;
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
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return PlanningItems
     */
    public function getPlanningItem()
    {
        return $this->planningItem;
    }

    /**
     * @param mixed $planningItem
     */
    public function setPlanningItem($planningItem)
    {
        $this->planningItem = $planningItem;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getSubQuantity()
    {
        return $this->subQuantity;
    }

    /**
     * @param mixed $subQuantity
     */
    public function setSubQuantity($subQuantity)
    {
        $this->subQuantity = $subQuantity;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
                'name'     => 'orderQuantity',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'spare',
                'required' => true,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
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
            'itemPrice' => $this->getPrice(),
            'orderItemId' => $this->getId(),
            'countInOrder' => $this->getQuantity(), //заказанное количество, без учета составных запчастей
            'subCount' => $this->getSubQuantity(),
            'nameSpare' => $this->getSpare()->getName(),
            'spareUnits' => $this->getSpare()->getUnits(),
            'isComposite' => ! empty($this->getSubQuantity()),
            'countReceipted' => $this->getReceipted(false),//полученное количество, без учета составных запчастей
        ];
    }
}
