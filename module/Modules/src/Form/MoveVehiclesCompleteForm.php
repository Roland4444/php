<?php

namespace Modules\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Modules\Entity\MoveVehiclesEntity;
use Zend\Form\Form;

class MoveVehiclesCompleteForm extends Form
{
    /**
     * MoveVehiclesCompleteForm constructor.
     *
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct('vehicle');

        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new MoveVehiclesEntity());

        $this->add([
            'name' => 'waybill',
            'type' => 'Text',
            'options' => [
                'label' => 'Номер накладной:',
            ],
            'attributes' => [
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
                'type'  => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
