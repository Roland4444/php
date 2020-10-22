<?php

namespace Storage\Form;

use Storage\Entity\MetalExpense;
use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Form\Element\EntitySelect;
use Reference\Entity\Department;

class CashTransferForm extends Form
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

    public function __construct($entityManager, Department $department)
    {
        parent::__construct('cash_transfer');
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
            'type' => EntitySelect::class,
            'name' => 'dest',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $entityManager,
                'target_class' => Department::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findWithoutMe',
                    'params' => [
                        'departmentId' => $department->getId(),
                        'type' => $department->getType(),
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'id' => 'dest',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'money',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => false,
                'id' => 'money',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Сумма:',
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
