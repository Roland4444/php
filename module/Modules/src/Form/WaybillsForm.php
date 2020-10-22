<?php

namespace Modules\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Modules\Entity\Waybill;
use Modules\Entity\WaybillSettings;
use Reference\Entity\Employee;
use Reference\Entity\Vehicle;
use Zend\Form\Form;

class WaybillsForm extends Form
{
    /**
     * WaybillsForm constructor.
     * @param int|null|string $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new Waybill());

        $this->add([
            'name' => 'waybillNumber',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'Номер:',
            ],
        ]);

        $this->add([
            'name' => 'vehicle',
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'options' => [
                'label' => 'Транспорт:',
                'object_manager' => $entityManager,
                'target_class' => Vehicle::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findNotArchival'
                ]
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'id' => 'vehicle',
            ]
        ]);


        $this->add([
            'name' => 'driver',
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'options' => [
                'label' => 'Водитель:',
                'object_manager' => $entityManager,
                'target_class' => Employee::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findDrivers',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'id' => 'driver',
            ]
        ]);

        $this->add([
            'name' => 'dateStart',
            'attributes' => [
                'type' => 'text',
                'id' => 'dateStart',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата выезда:',
            ],
        ]);

        $this->add([
            'name' => 'dateEnd',
            'attributes' => [
                'type' => 'text',
                'id' => 'dateEnd',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата прибытия:',
            ],
        ]);

        $this->add([
            'name' => 'speedometerStart',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
                'autocomplete' => 'off',
                'required' => true,
            ],
            'options' => [
                'label' => 'Показание спидометра при выезде:',
            ],
        ]);

        $this->add([
            'name' => 'speedometerEnd',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
                'autocomplete' => 'off',
                'required' => true,
            ],
            'options' => [
                'label' => 'Показание спидометра при возвращении:',
            ],
        ]);

        $this->add([
            'name' => 'fuelStart',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
                'autocomplete' => 'off',
                'required' => true,
            ],
            'options' => [
                'label' => 'Остаток топлива при выезде:',
            ],
        ]);

        $this->add([
            'name' => 'fuelEnd',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
                'autocomplete' => 'off',
                'required' => true,
            ],
            'options' => [
                'label' => 'Остаток топлива при возвращении:',
            ],
        ]);

        $this->add([
            'name' => 'refueled',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'Дозаправлено литров (выдано):',
            ],
        ]);

        $this->add([
            'name' => 'specialEquipmentTime',
            'attributes' => [
                'type' => 'text',
                'id' => 'specialEquipmentTime',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Время работы спец оборудования(ч):',
            ],
        ]);

        $this->add([
            'name' => 'engineTime',
            'attributes' => [
                'type' => 'text',
                'id' => 'engineTime',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Время работы двигателя(ч):',
            ],
        ]);

        $this->add([
            'name' => WaybillSettings::CHANGE_FACTOR,
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'class' => 'form-control',
                'required' => true,
                'dopElement' => true,
            ],
            'options' => [
                'label' => 'Коэффицент изменения нормы:',
            ],
        ]);

        $this->add([
            'name' => WaybillSettings::DISPATCHER,
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
                'autocomplete' => 'off',
                'dopElement' => true,
            ],
            'options' => [
                'label' => 'Диспетчер:',
            ],
        ]);

        $this->add([
            'name' => WaybillSettings::MECHANIC,
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
                'autocomplete' => 'off',
                'dopElement' => true,
            ],
            'options' => [
                'label' => 'Механик:',
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
