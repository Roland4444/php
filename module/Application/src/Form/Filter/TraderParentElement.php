<?php

namespace Application\Form\Filter;

use Reference\Entity\TraderParent;

class TraderParentElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'traderParent',
            'options' => [
                'label' => 'Группа трейдеров:',
                'object_manager' => $this->entityManager,
                'target_class' => TraderParent::class,
                'property' => 'name',
                'empty_option' => 'Выбрать группу',
                'find_method' => [
                    'name' => 'findAll',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'value' => isset($params['traderParent']) ? $params['traderParent'] : '',
                'class' => 'form-control',
            ]
        ]);
        return $form;
    }
}
