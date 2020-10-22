<?php

namespace Finance\Form;

use Finance\Entity\BankAccount;
use Finance\Entity\TraderReceipts;
use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class TraderReceiptsForm extends Form
{
    public function __construct($entityManager)
    {
        parent::__construct('TraderReceiptsForm');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new TraderReceipts());

        $this->add([
            'name' => 'date',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'trader',
            'options' => [
                'label' => 'Трейдер:',
                'object_manager' => $entityManager,
                'target_class' => 'Reference\Entity\Trader',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
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
            'name' => 'money',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Сумма:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'options' => [
                'label' => 'Тип:',
                'value_options' => [
                    'black' => 'Черный',
                    'color' => 'Цветной'
                ],
            ],
            'attributes' => [
                'value' => '1', //set selected to '1'
                'class' => 'form-control',
            ]
        ]);
    }
}
