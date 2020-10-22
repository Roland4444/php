<?php

namespace Storage\Form;

use Doctrine\ORM\EntityManager;
use Storage\Entity\Shipment;
use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\FormInterface;
use Zend\Form\Element\Hidden;
use DoctrineORMModule\Form\Element\EntitySelect;
use Reference\Entity\Trader;
use Reference\Entity\ShipmentTariff;
use Zend\Form\Element\Checkbox;

class ShipmentForm extends Form
{
    private EntityManager $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct('shipment_form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'shipment_form');
        $this->setHydrator(new DoctrineObject($this->entityManager))->setObject(new Shipment());
    }

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

    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ($object->hasOption('factoring')) {
            $this->get('factoring')->setValue(1);
        }
        return parent::bind($object, $flags);
    }


    public function addElements(string $type, bool $showRate): void
    {
        $this->add([
            'type' => Hidden::class,
            'name' => 'type',
            'attributes' => [
                'value' => $type,
                'id' => 'type',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'date',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
                'value' => date('Y-m-d'),
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $this->add([
            'type' => EntitySelect::class,
            'name' => 'trader',
            'options' => [
                'label' => 'Трейдер:',
                'object_manager' => $this->entityManager,
                'target_class' => Trader::class,
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
                'id' => 'trader',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => EntitySelect::class,
            'name' => 'tariff',
            'options' => [
                'label' => 'Тариф:',
                'object_manager' => $this->entityManager,
                'target_class' => ShipmentTariff::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['type' => $type],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'id' => 'tariff',
                'class' => 'form-control',
            ]
        ]);

        if ($showRate) {
            $this->add([
                'name' => 'rate',
                'attributes' => [
                    'type' => 'number',
                    'step' => '0.0001',
                    'min' => '0',
                    'id' => 'rate',
                    'class' => 'form-control',
                ],
                'options' => [
                    'label' => 'Курс доллара:',
                ],
            ]);
        } else {
            $this->add([
                'type' => Hidden::class,
                'name' => 'rate',
                'attributes' => [
                    'value' => '',
                    'id' => 'rate',
                    'class' => 'form-control',
                ]
            ]);
        }

        $this->add([
            'type' => Checkbox::class,
            'name' => 'factoring',
            'options' => [
                'label' => 'Факторинг',
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
                'id' => 'submit_shipment',
            ],
        ]);
    }
}
