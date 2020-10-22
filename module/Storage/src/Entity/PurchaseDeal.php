<?php

namespace Storage\Entity;

use chillerlan\QRCode\QRCode;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Regex;

/**
 * @ORM\Entity
 * @ORM\Table(name="purchase_deal")
 */
class PurchaseDeal implements InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $code;

    /** @ORM\Column(type="string") */
    private $comment;

    /**
     *
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="deal",cascade={"persist","remove"}))
     */
    private $purchaseList;

    /**
     *
     * @ORM\OneToMany(targetEntity="MetalExpense", mappedBy="deal",cascade={"persist","remove"}))
     */
    private $payments;

    public function __construct($code = null, $comment = null, $purchaseList = null)
    {
        if ($code) {
            $this->code = $code;
        }
        if ($comment) {
            $this->comment = $comment;
        }
        if ($purchaseList) {
            $this->purchaseList = $purchaseList;
            /** @var Purchase $purchase */
            foreach ($purchaseList as $purchase) {
                $purchase->setDeal($this);
            }
        }
    }

    public function getWeight()
    {
        $weight = 0;
        foreach ($this->purchaseList as $purchase) {
            $weight = bcadd($weight, $purchase->getWeight(), 16);
        }
        return $weight;
    }

    public function getSum()
    {
        $sum = 0;
        /** @var Purchase $purchase */
        foreach ($this->purchaseList as $purchase) {
            $sum = bcadd($sum, $purchase->getSum(), 16);
        }
        return $sum;
    }

    public function isPaid()
    {
        $paid = 0;
        foreach ($this->getPayments() as $payment) {
            $paid = bcadd($paid, $payment->getMoney(), 16);
        }
        return $paid == $this->getSum();
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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getQR()
    {
        return (new QRCode())->render($this->getCode());
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getPurchaseList()
    {
        return $this->purchaseList;
    }

    /**
     * @param mixed $purchaseList
     */
    public function setPurchaseList($purchaseList): void
    {
        $this->purchaseList = $purchaseList;
    }

    /**
     * @return mixed
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param mixed $payments
     */
    public function setPayments($payments): void
    {
        $this->payments = $payments;
    }

    /**
     * Set input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @return void
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Retrieve input filter
     *
     * @return InputFilter
     */
    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();
            $this->inputFilter = $inputFilter;

            $inputFilter->add([
                'name' => 'comment',
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[a-zA-Zа-яА-Я0-9.,% \/\*()\-"@]{1,255}$/ui',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
                        ],
                    ],
                ],
            ]);
        }
        return $this->inputFilter;
    }
}
