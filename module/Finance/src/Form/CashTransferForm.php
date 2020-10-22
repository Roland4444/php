<?php

namespace Finance\Form;

use Finance\Entity\BankAccount;
use Finance\Entity\CashTransfer;
use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class CashTransferForm extends Form
{
    public function __construct($entityManager)
    {
        parent::__construct('CashTransferForm');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new CashTransfer());

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'source',
            'options' => [
                'label' => 'Источник:',
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
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'dest',
            'options' => [
                'label' => 'Получатель:',
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
    }
}
