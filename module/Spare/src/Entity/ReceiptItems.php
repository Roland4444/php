<?php
namespace Spare\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="spare_receipt_items")
 */
class ReceiptItems implements InputFilterAwareInterface, \JsonSerializable
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Receipt", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="receipt_id", referencedColumnName="id")
     */
    private $receipt;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\OrderItems", inversedBy="itemsReceipt")
     * @ORM\JoinColumn(name="order_item_id", referencedColumnName="id")
     */
    private $orderItem;

    /** @ORM\Column(type="decimal") */
    private $quantity;

    /** @ORM\Column(type="decimal", name="sub_quantity") */
    private $subQuantity;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\Spare", fetch="EAGER")
     * @ORM\JoinColumn(name="spare_id", referencedColumnName="id")
     */
    private $spare;

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
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @param mixed $receipt
     */
    public function setReceipt($receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * @return OrderItems
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param mixed $orderItem
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;
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
                'name'     => 'quantity',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'receipt',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'orderItem',
                'required' => true,
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
           'id' => $this->getId(),
           'orderItemId' => $this->getOrderItem()->getId(),
           'itemPrice' => $this->getOrderItem()->getPrice(),
           'nameSpare' => $this->getSpare()->getName(),
           'spareUnits' => $this->getSpare()->getUnits(),
           'count' => $this->getQuantity(),
           'countInOrder' => $this->getOrderItem()->getQuantity(),
           'date' => $this->getOrderItem()->getOrder()->getDate(),
           'number' => $this->getOrderItem()->getOrder()->getId(),
           'isComposite' => $this->getSpare()->getIsComposite(),
           'subCount' => $this->getSubQuantity(),
           'countRestForReceipt' => $this->getOrderItem()->getQuantity() - $this->getOrderItem()->getReceipted(false) + $this->getQuantity(),
           'orderDate' => $this->getOrderItem()->getOrder()->getDate()
        ];
    }
}
