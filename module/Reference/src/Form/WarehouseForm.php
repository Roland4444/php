<?php

namespace Reference\Form;

use Core\Utils\Options;
use Reference\Entity\Department;
use Reference\Entity\User;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilter;

/**
 * Class CategoryForm
 * @package Reference\Form
 */
class WarehouseForm extends AbstractReferenceForm
{
    /**
     * CategoryForm constructor.
     */
    public function __construct($entityManager)
    {
        parent::__construct('category', $entityManager);
        $this->setAttribute('method', 'post');
    }

    public function addElements()
    {
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Наименование:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => Options::CLOSED,
            'options' => [
                'label' => 'Хоз. склад закрыт',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntityMultiCheckbox',
            'name' => 'users',
            'options' => [
                'label' => 'Пользователи:',
                'object_manager' => $this->entityManager,
                'target_class' => User::class,
                'property' => 'name',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'name',
                'required' => true,
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
                            'max' => 40,
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * Собирает форму и наполняет ее данными из сущности
     *
     * @param Department $object
     * @param int $flags
     * @return \Zend\Form\Form
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ($object->isClosed()) {
            $this->get(Options::CLOSED)->setValue(1);
        }

        return parent::bind($object, $flags);
    }
}
