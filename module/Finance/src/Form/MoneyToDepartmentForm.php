<?php

namespace Finance\Form;

use Finance\Entity\BankAccount;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Form;

class MoneyToDepartmentForm extends Form
{
    public function __construct($entityManager)
    {
        parent::__construct('MoneyToDepartmentForm');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new \Finance\Entity\MoneyToDepartment());

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'bank',
            'options' => [
                'label' => 'Счет:',
                'object_manager' => $entityManager,
                'target_class' => BankAccount::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['closed' => 0],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'date',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
                'class' => 'form-control ui-date',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'department',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $entityManager,
                'target_class' => 'Reference\Entity\Department',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findOpened',
                    'params' => [
                        'isAdmin' => true,
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'money',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'class' => 'form-control',
                'id' => 'money'
            ],
            'options' => [
                'label' => 'Сумма:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'verified',
            'options' => [
                'label' => 'Подтвердить',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ]
        ]);
    }
}
