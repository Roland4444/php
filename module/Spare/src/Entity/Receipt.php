<?php
namespace Spare\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Reference\Entity\Warehouse;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\ReceiptRepository")
 * @ORM\Table(name="spare_receipt")
 */
class Receipt implements InputFilterAwareInterface, AbstractEntity, \JsonSerializable
{
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
     * @ORM\JoinColumn(name="seller", referencedColumnName="id")
     */
    private $seller;

    /** @ORM\OneToMany(targetEntity="ReceiptItems", mappedBy="receipt", cascade={"persist", "remove"},
     *     orphanRemoval=true, fetch="EAGER") */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }
    public function getDate()
    {
        return $this->date ? $this->date : date('Y-m-d');
    }

    public function setSeller($seller)
    {
        $this->seller = $seller;
    }

    /**
     * @return Seller
     */
    public function getSeller()
    {
        return $this->seller;
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
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    public function clearItems()
    {
        return $this->items->clear();
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     * @param ReceiptItems $receiptItem
     */
    public function addItem(ReceiptItems $receiptItem)
    {
        $this->items->add($receiptItem);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'document',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'provider',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'date',
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
            'date' => $this->getDate(),
            'documentName' => $this->getDocument(),
            'provider' => $this->getSeller()->getId(),
            'items' => $this->getItems()->toArray()
        ];
    }
}
