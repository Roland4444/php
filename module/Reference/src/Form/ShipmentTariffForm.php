<?php
namespace Reference\Form;

class ShipmentTariffForm extends AbstractReferenceForm
{

    public function __construct()
    {
        parent::__construct('tariff');
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
            'name' => 'destination',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Завод:',
            ],
        ]);

        $this->add([
            'name' => 'distance',
            'attributes' => [
                'type' => 'number',
                'step' => '1',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Дистанция:',
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
                'class' => 'form-control',
            ],
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
            'name' => 'money',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Оплата собственнику:',
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
