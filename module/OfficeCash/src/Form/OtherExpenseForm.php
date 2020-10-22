<?php

namespace OfficeCash\Form;

use OfficeCash\Entity\OtherExpense;
use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class OtherExpenseForm extends Form
{
    public function prepare()
    {
        echo "
			<script>
				$(document).ready(() => {
				    $('#date').datepicker();
				    $('#realdate').datepicker();
			    });
			</script>
		";
    }

    public function __construct($entityManager, $roleIds)
    {
        parent::__construct('other_expense');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new OtherExpense());

        $this->add([
            'name' => 'date',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
                'class' => 'form-control',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'category',
            'options' => [
                'label' => 'Категория:',
                'object_manager' => $entityManager,
                'target_class' => 'Reference\Entity\Category',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findByRole',
                    'params' => [
                        'roles' => $roleIds,
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'id' => 'category',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'comment',
            'attributes' => [
                'type' => 'textarea',
                'class' => 'form-control',
                'rows' => 5,
            ],
            'options' => [

                'label' => 'Комментарий:',
            ],
        ]);

        $this->add([
            'name' => 'realdate',
            'attributes' => [
                'type' => 'text',
                'id' => 'realdate',
                'class' => 'form-control',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Дата расхода:',
            ],
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
