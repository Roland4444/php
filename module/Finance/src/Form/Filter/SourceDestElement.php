<?php

namespace Finance\Form\Filter;

use Application\Form\Filter\AElement;
use Finance\Entity\BankAccount;

/**
 * Class SourceDestElement
 * @package Finance\Form\Filter
 */
class SourceDestElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);

        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'source',
            'options' => [
                'label' => 'Категория:',
                'object_manager' => $this->entityManager,
                'target_class' => BankAccount::class,
                'property' => 'name',
                'empty_option' => 'Выбрать источник',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['closed' => 0],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'value' => isset($params['source']) ? $params['source'] : '',
                'class' => 'form-control',
            ]
        ]);

        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'dest',
            'options' => [
                'label' => 'Категория:',
                'object_manager' => $this->entityManager,
                'target_class' => BankAccount::class,
                'property' => 'name',
                'empty_option' => 'Выбрать получателя',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['closed' => 0],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'value' => isset($params['dest']) ? $params['dest'] : '',
                'class' => 'form-control',
            ]
        ]);

        return $form;
    }
}
