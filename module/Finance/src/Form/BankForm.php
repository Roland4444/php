<?php

namespace Finance\Form;

use Core\Entity\AbstractEntity;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Finance\Entity\BankAccount;
use Reference\Form\PlainForm;
use Zend\InputFilter\InputFilter;

/**
 * Class BankForm
 * @package Finance\Form
 */
class BankForm extends PlainForm
{
    protected $entityClass = BankAccount::class;

    /**
     * BankForm constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('bank', $entityManager);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new BankAccount());
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
                'label' => 'Счет:',
            ],
        ]);

        $this->add([
            'name' => 'bank',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Банк:',
            ],
        ]);

        $this->add([
            'name' => 'alias',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Алиас:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'cash',
            'options' => [
                'label' => 'Наличные',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
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
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'closed',
            'options' => [
                'label' => 'Закрыт',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'diamond',
            'options' => [
                'label' => 'Система алмаз',
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
                'name' => 'bank',
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
                'name' => 'cash',
                'required' => true,
            ]);
            $inputFilter->add([
                'name' => 'alias',
                'required' => false,
            ]);
            $inputFilter->add([
                'name' => 'def',
                'required' => true,
            ]);
            $inputFilter->add([
                'name' => 'closed',
                'required' => true,
            ]);
            $inputFilter->add([
                'name' => 'diamond',
                'required' => true,
            ]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
