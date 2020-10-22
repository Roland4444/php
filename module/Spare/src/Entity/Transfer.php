<?php
namespace Spare\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Spare\Repositories\TransferRepository")
 * @ORM\Table(name="spare_transfer")
 */
class Transfer implements InputFilterAwareInterface
{
    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="integer") */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="\Spare\Entity\Spare")
     * @ORM\JoinColumn(name="spare_id", referencedColumnName="id")
     */
    private $spare;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Warehouse")
     * @ORM\JoinColumn(name="source_id", referencedColumnName="id")
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="\Reference\Entity\Warehouse")
     * @ORM\JoinColumn(name="dest_id", referencedColumnName="id")
     */
    private $dest;

    /** @ORM\Column(type="string") */
    private $date;

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
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getDest()
    {
        return $this->dest;
    }

    /**
     * @param mixed $dest
     */
    public function setDest($dest)
    {
        $this->dest = $dest;
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
