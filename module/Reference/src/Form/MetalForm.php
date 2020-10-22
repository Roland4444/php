<?php

namespace Reference\Form;

use Zend\InputFilter\InputFilter;

class MetalForm extends AbstractReferenceForm
{
    public function __construct($entityManager)
    {
        parent::__construct('metal', $entityManager);
        $this->setAttribute('method', 'post');
    }

    public function addElements()
    {
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Наименование:',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'group',
            'options' => [
                'label' => 'Группа:',
                'object_manager' => $this->entityManager,
                'target_class' => 'Reference\Entity\MetalGroup',
                'property' => 'name',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'code',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Код:',
            ],
        ]);

        $this->add([
            'name' => 'alias',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Псевдоним:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'def',
            'options' => [
                'label' => 'По умолчанию',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
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
            $inputFilter->add([
                'name' => 'group',
                'required' => true,
            ]);
            $inputFilter->add([
                'name' => 'def',
                'required' => true,
            ]);
            $inputFilter->add([
                'name' => 'code'
            ]);
            $inputFilter->add([
                'name' => 'alias'
            ]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
