<?php

namespace Reference\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Class Settings
 *
 * @ORM\Entity(repositoryClass="\Reference\Repositories\SettingRepository")
 * @ORM\Table(name="settings")
 */
class Settings implements InputFilterAwareInterface, AbstractEntity
{
    const DIAMOND_COMMISSION = 'diamond_commission';

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $alias;

    /**
     * @ORM\Column(type="string")
     */
    private $value;

    /**
     * @ORM\Column(type="string")
     */
    private $label;

    /**
     * Settings constructor.
     *
     * @param $alias
     * @param $value
     * @param $label
     */
    public function __construct($value = null, $label = null, $alias = null)
    {
        $this->alias = $alias;
        $this->value = $value;
        $this->label = $label;
    }


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = trim($alias);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = str_replace(',', '.', trim($value));
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
            $inputFilter->add(['name' => 'alias', 'required' => true]);
            $inputFilter->add(['name' => 'value', 'required' => true]);
            $inputFilter->add(['name' => 'label', 'required' => true]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
