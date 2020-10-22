<?php

namespace Reference\Form;

use Reference\Entity\Customer;

/**
 * Class CustomerDebtForm
 * @package Reference\Form
 */
class CustomerDebtForm extends AbstractReferenceForm
{
    protected $entityManager;

    public function __construct($entityManager)
    {
        parent::__construct('customer-debt', $entityManager);
        $this->setAttribute('method', 'post');
    }

    /**
     * @return mixed|void
     */
    public function addElements()
    {
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
            'name' => 'customer',
            'options' => [
                'label' => 'Поставщик:',
                'object_manager' => $this->entityManager,
                'target_class' => Customer::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findActive',
                    'params' => [
                        'orderBy'  => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);
        $this->add([
            'name' => 'amount',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => true,
                'id' => 'amount',
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
