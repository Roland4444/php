<?php

namespace Application\Form\Filter;

use Reference\Entity\Customer;

/**
 * Class CustomerElement
 * @package Application\Form\Filter
 */
class CustomerElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'customer',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $this->entityManager,
                'target_class' => Customer::class,
                'property' => 'name',
                'empty_option' => 'Выбрать поставщика',
                'find_method' => [
                    'name' => 'findActive',
                    'params' => [
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'value' => isset($params['customer']) ? $params['customer'] : '',
                'class' => 'form-control',
            ]
        ]);
        return $form;
    }
}
