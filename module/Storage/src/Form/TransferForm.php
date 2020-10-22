<?php

namespace Storage\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Department;
use Storage\Entity\Transfer;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterAwareInterface;

class TransferForm extends Form implements InputFilterAwareInterface
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

    public function __construct($em, $departmentId)
    {
        parent::__construct('metal_expense');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($em))->setObject(new Transfer());

        $this->add([
            'name' => 'date',
            'type' => 'DateTime',
            'attributes' => [
                'id' => 'date',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата:',
                'format' => 'Y-m-d'
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'dest',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $em,
                'target_class' => Department::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findWithoutMe',
                    'params' => [
                        'departmentId' => $departmentId,
                        'type' => null,
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
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'metal',
            'options' => [
                'label' => 'Металл:',
                'object_manager' => $em,
                'target_class' => 'Reference\Entity\Metal',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'asc'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'id' => 'metal',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'weight',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => true,
                'id' => 'weight',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Масса:',
            ],
        ]);

        $this->add([
            'name' => 'actual',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => false,
                'id' => 'actual',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Факт. масса:',
            ],
        ]);

        $this->add([
            'name' => 'nakl1',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => false,
                'id' => 'nakl1',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Накладная 1:',
            ],
        ]);

        $this->add([
            'name' => 'nakl2',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => false,
                'id' => 'nakl2',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Накладная 2:',
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
