<?php
namespace Spare\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Finance\Entity\OtherExpense;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\OrderRepository")
 * @ORM\Table(name="spare_order")
 */
class Order implements AbstractEntity, InputFilterAwareInterface, \JsonSerializable
{
    const STATUS_NEW = 'Новый';
    const STATUS_IN_WORK = 'В работе';
    const STATUS_CLOSED = 'Закрыт';

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_WORK,
        self::STATUS_CLOSED,
    ];

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $date;

    /** @ORM\Column(type="string") */
    private $document;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\Seller")
     * @ORM\JoinColumn(name="seller_id", referencedColumnName="id")
     */
    private $seller;

    /** @ORM\OneToMany(targetEntity="OrderItems", mappedBy="order", cascade={"all"}, orphanRemoval=true) */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\OrderStatus", fetch="EAGER")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\OrderPaymentStatus", fetch="EAGER")
     * @ORM\JoinColumn(name="payment_status_id", referencedColumnName="id")
     */
    private $paymentStatus;

    /**
     * @ORM\ManyToMany(targetEntity="\Finance\Entity\OtherExpense")
     * @ORM\JoinTable(name="spare_order_expense_ref",
     *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="expense_id", referencedColumnName="id")}
     *      )
     */
    private $expenses;

    /**
     * @ORM\ManyToMany(targetEntity="\OfficeCash\Entity\OtherExpense")
     * @ORM\JoinTable(name="spare_order_cash_expense_ref",
     *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="expense_id", referencedColumnName="id")}
     *      )
     */
    private $cashExpenses;

    /** @ORM\Column(name="expected_date", type="string") */
    private $expectedDate;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->expenses = new ArrayCollection();
        $this->cashExpenses = new ArrayCollection();
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
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param mixed $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return Seller
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @param mixed $seller
     */
    public function setSeller($seller)
    {
        $this->seller = $seller;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items): void
    {
        foreach ($items as $item) {
            $item->setOrder($this);
        }
        $this->items = $items;
    }

    public function addItems(array $items): void
    {
        foreach ($items as $item) {
            $item->setOrder($this);
            $this->items->add($item);
        }
    }

    public function removeItem(OrderItems $entity)
    {
        $this->items->removeElement($entity);
    }

    /**
     * Возвращает общую цену заказа
     *
     * @return int|float
     */
    public function getPrice()
    {
        $price = 0;
        foreach ($this->items as $itemOrder) { /**@var OrderItems $itemOrder*/
            $price += $itemOrder->getOrderItemsPrice();
        }
        return $price;
    }

    /**
     * @return OrderStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return OrderPaymentStatus
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @param mixed $paymentStatus
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @return ArrayCollection
     */
    public function getExpenses()
    {
        return $this->expenses;
    }

    /**
     * @param mixed $expenses
     */
    public function addExpenses(?OtherExpense $expenses)
    {
        $this->expenses->add($expenses);
    }

    /**
     * @return ArrayCollection
     */
    public function getCashExpenses()
    {
        return $this->cashExpenses;
    }

    /**
     * @param mixed $expenses
     */
    public function addCashExpenses(?\OfficeCash\Entity\OtherExpense $expenses)
    {
        $this->cashExpenses->add($expenses);
    }

    /**
     * @return mixed
     */
    public function getExpectedDate()
    {
        return $this->expectedDate;
    }

    /**
     * @param mixed $expectedDate
     */
    public function setExpectedDate($expectedDate)
    {
        $this->expectedDate = $expectedDate;
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
                'name'     => 'date',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'document',
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
            'date' => $this->getDate(),
            'documentName' => $this->getDocument(),
            'seller' => $this->getSeller()->getName(),
            'items' => $this->getItems()->toArray()
        ];
    }
}
