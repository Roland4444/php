<?php

namespace Storage\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Customer;
use Zend\Form\Form;

class EditDateCustomer extends Form
{
    public function __construct($entityManager)
    {
        parent::__construct('customer');
        $this->setAttribute('method', 'post');
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new Customer());

        $this->add([
            'name' => 'inspectionDate',
            'attributes' => [
                'type' => 'text',
                'required' => false,
                'id' => 'inspectionDate',
                'class' => 'form-control',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Дата сверки:',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
            ],
        ]);
    }
}
