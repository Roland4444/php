<?php

namespace Storage\Form;

use Zend\Form\Form;
use Reference\Entity\ContainerOwner;
use DoctrineORMModule\Form\Element\EntitySelect;

class ContainerForm extends Form
{
    public function __construct($entityManager)
    {
        parent::__construct('container');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'container_form');

        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'id' => 'name',
                'required' => true,
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Номер:',
            ],
        ]);

        $this->add([
            'type' => EntitySelect::class,
            'name' => 'owner',
            'options' => [
                'label' => 'Собственник:',
                'object_manager' => $entityManager,
                'target_class' => ContainerOwner::class,
                'property' => 'name',
            ],
            'attributes' => [
                'required' => true,
                'id' => 'owner',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submit_container',
            ],
        ]);
    }
}
