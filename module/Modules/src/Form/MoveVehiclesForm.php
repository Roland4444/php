<?php

namespace Modules\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Modules\Entity\MoveVehiclesEntity;
use Zend\Form\Form;

class MoveVehiclesForm extends Form
{
    public function __construct($em = null)
    {
        parent::__construct('vehicle');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($em))->setObject(new MoveVehiclesEntity());

        $this->add([
            'name' => 'date',
            'type' => 'Date',
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
            'name' => 'customer',
            'type' => 'Text',
            'options' => [
                'label' => 'Клиент:',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'driver',
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'options' => [
                'label' => 'Водитель:',
                'object_manager' => $em,
                'target_class' => 'Reference\Entity\Employee',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findDrivers',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'vehicle',
            'options' => [
                'label' => 'Техника:',
                'object_manager' => $em,
                'target_class' => 'Reference\Entity\Vehicle',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findMovable',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'departure',
            'type' => 'text',
            'options' => [
                'label' => 'Адрес пункта погрузки:',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'arrival',
            'type' => 'text',
            'options' => [
                'label' => 'Адрес пункта разгрузки:',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'payment',
            'type' => 'Number',
            'options' => [
                'label' => 'Оплата:',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'department',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $em,
                'target_class' => 'Reference\Entity\Department',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findOpened',
                    'params' => [
                        'isAdmin' => false,
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'comment',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Комментарий:',
            ],
            'attributes' => [
                'required' => false,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
