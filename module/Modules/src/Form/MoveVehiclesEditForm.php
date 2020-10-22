<?php

namespace Modules\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Modules\Entity\MoveVehiclesEntity;
use Zend\Form\Form;

class MoveVehiclesEditForm extends Form
{
    public function prepare()
    {
        echo "
			<script>
				$(document).ready(function(){
				    $('#date').datepicker();
			    });
			</script>
		";
    }

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
            'name' => 'departureTime',
            'attributes' => [
                'type' => 'text',
                'id' => 'departureTime',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Время выезда с базы:',
            ],
        ]);

        $this->add([
            'name' => 'arrivalAtPointTime',
            'attributes' => [
                'type' => 'text',
                'id' => 'arrivalAtPointTime',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Время прибытия на точку назначения:',
            ],
        ]);

        $this->add([
            'name' => 'departureFromPointTime',
            'attributes' => [
                'type' => 'text',
                'id' => 'departureFromPointTime',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Время убытия из точки назначения:',
            ],
        ]);

        $this->add([
            'name' => 'arrivalTime',
            'attributes' => [
                'type' => 'text',
                'id' => 'arrivalTime',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Время прибытия на базу:',
            ],
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
            'name' => 'distance',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'Количество пройденных км в одну сторону:',
            ],
        ]);

        $this->add([
            'name' => 'waybill',
            'type' => 'Text',
            'options' => [
                'label' => 'Номер накладной:',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'comment',
            'type' => 'Textarea',
            'options' => [
                'label' => 'Комментарий:',
            ],
            'attributes' => [
                'class' => 'form-control',
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
}
