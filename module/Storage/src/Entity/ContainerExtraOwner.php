<?php
namespace Storage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Reference\Entity\ContainerOwner;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Modules\Repository\ContainerExtraRepository")
 * @ORM\Table(name="container_extra_owner")
 */
class ContainerExtraOwner implements InputFilterAwareInterface, \JsonSerializable
{

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Container", inversedBy="extraOwner", cascade={"persist"})
     * @ORM\JoinColumn(name="container_id", referencedColumnName="id")
     */
    private $container;

    /**
     * @ORM\ManyToOne(targetEntity="Reference\Entity\ContainerOwner")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /** @ORM\Column(name="owner_cost",type="string") */
    private $ownerCost;

    /** @ORM\Column(name="date_formal",type="date") */
    private $dateFormal;

    /** @ORM\Column(name="is_paid",type="boolean") */
    private $isPaid;

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }
    public function getContainer()
    {
        return $this->container;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
    public function getOwner(): ?ContainerOwner
    {
        return $this->owner;
    }

    public function setOwnerCost($cost)
    {
        $this->ownerCost = $cost;
    }
    public function getOwnerCost()
    {
        return $this->ownerCost;
    }

    public function setDateFormal($date)
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        $this->dateFormal = $date;
    }
    public function getDateFormal(): ?\DateTime
    {
        return $this->dateFormal;
    }
    public function getDateFormalString()
    {
        return $this->dateFormal ? $this->dateFormal->format('Y-m-d') : null;
    }

    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;
    }
    public function getIsPaid()
    {
        return $this->isPaid;
    }

    /**
     * @param InputFilterInterface $inputFilter
     * @return void|InputFilterAwareInterface
     * @throws Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new Exception("Not used");
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'dateFormalString' => $this->getDateFormalString(),
            'ownerName' => $this->getOwner()->getName(),
            'isPaid' => $this->getIsPaid(),
            'ownerCost' => $this->getOwnerCost(),
        ];
    }
}
