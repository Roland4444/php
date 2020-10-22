<?php

namespace Storage\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Customer;
use Storage\Entity\MetalExpense;
use Zend\Form\Form;
use Zend\Form\FormInterface;

class MetalExpenseForm extends Form
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

    public function __construct($entityManager, $dep, $categoryExcludes)
    {
        parent::__construct('metal_expense');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new MetalExpense());

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
            'name' => 'customer',
            'options' => [
                'label' => 'Поставщик:',
                'disable_inarray_validator' => true,
                'object_manager' => $entityManager,
                'target_class' => 'Reference\Entity\Customer',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findUsed',
                    'params' => [
                        'dep' => $dep,
                        'excludes' => $categoryExcludes
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'id' => 'customer',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'money',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => true,
                'id' => 'money',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Сумма:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'diamond',
            'options' => [
                'label' => 'Оплата на карту',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'formal',
            'options' => [
                'label' => 'Официальная оплата',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
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
        $customerSelect = $this->get('customer');
        $options = $customerSelect->getValueOptions();

        /** @var Customer $customer */
        $customer = $object->getCustomer();

        if ($customer) {
            foreach ($options as $option) {
                if ($option['value'] == $customer->getId()) {
                    parent::bind($object, $flags);
                    return;
                }
            }

            $options[] = [
                'label' => $customer->getName(),
                'value' => $customer->getId(),
                'attributes' => [],
            ];

            $customerSelect->setValueOptions($options);
        }
        parent::bind($object, $flags);
    }
}
