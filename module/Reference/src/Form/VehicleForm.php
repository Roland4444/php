<?php

namespace Reference\Form;

use Core\Utils\Options;
use Zend\Form\FormInterface;

class VehicleForm extends AbstractReferenceForm
{

    public function __construct($entityManager)
    {
        parent::__construct('vehicle', $entityManager);
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
                'label' => 'Имя:',
            ],
        ]);

        $this->add([
            'name' => 'model',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Модель автомобиля:',
            ],
        ]);

        $this->add([
            'name' => 'number',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Номер:',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'department',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $this->entityManager,
                'target_class' => 'Reference\Entity\Department',
                'property' => 'name',
                'empty_option' => 'Все площадки',
                'find_method' => [
                    'name' => 'findOpened',
                    'params' => [
                        'isAdmin' => true,
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'specialEquipmentConsumption',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Потребление топлива оборудованием (л/ч):',
            ],
        ]);

        $this->add([
            'name' => 'engineConsumption',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Потребление топлива двигателем (л/ч):',
            ],
        ]);

        $this->add([
            'name' => 'fuelConsumption',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Потребление топлива двигателем (л на 100 км):',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'movable',
            'options' => [
                'label' => 'Передвижная:',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'archival',
            'options' => [
                'label' => 'Архивная:',
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

    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ($object->isMovable()) {
            $this->get(Options::MOVABLE)->setValue(1);
        }
        if ($object->isArchived()) {
            $this->get(Options::ARCHIVAL)->setValue(1);
        }
        return parent::bind($object, $flags);
    }
}
