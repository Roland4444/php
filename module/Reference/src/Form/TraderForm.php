<?php
namespace Reference\Form;

use Zend\InputFilter\InputFilter;

/**
 * Class TraderForm
 * @package Reference\Form
 */
class TraderForm extends AbstractReferenceForm
{

    /**
     * TraderForm constructor.
     * @param int|null|string $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct('trader', $entityManager);
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
            'name' => 'parent',
            'options' => [
                'label' => 'Группа:',
                'object_manager' => $this->entityManager,
                'target_class' => 'Reference\Entity\TraderParent',
                'property' => 'name',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
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
            'name' => 'inn',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'ИНН:',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
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
                            'max'      => 40,
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'parent',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'def',
                'required' => true,
            ]);

            $inputFilter->add([
                'name'     => 'inn',
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
                            'max'      => 40,
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
