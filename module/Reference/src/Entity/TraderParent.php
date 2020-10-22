<?php
namespace Reference\Entity;

use Core\Entity\EntityWithOptions;
use Core\Utils\Options;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Class MetalGroup
 * @ORM\Entity
 * @ORM\Table(name="trader_parent")
 */
class TraderParent implements InputFilterAwareInterface
{
    use EntityWithOptions;

    protected $inputFilter;

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(type="string") */
    private $name;

    /** @ORM\Column(type="string", name="ord") */
    private $order;

    /** @ORM\Column(type="string") */
    private $alias;

    /** @ORM\Column(type="json") */
    private $options;

    /** @ORM\OneToMany(targetEntity="Trader", mappedBy="parent",cascade={"persist","remove"})) */
    private $traders;

    /**
     * @return int|boolean
     */
    public function getHide()
    {
        return (int)$this->isHide();
    }

    /**
     * @param int|boolean $hide
     */
    public function setHide($hide)
    {
        $this->setOption(Options::HIDE, (boolean)$hide);
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Проверяет, скрыт ли
     *
     * @return bool
     */
    public function isHide()
    {
        return $this->hasOption(Options::HIDE);
    }

    public function getTraders()
    {
        return $this->traders;
    }

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

    public function setOrder($order)
    {
        $this->order = $order;
    }
    public function getOrder()
    {
        return $this->order;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    public function getAlias()
    {
        return $this->alias;
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
                'name'     => 'name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
