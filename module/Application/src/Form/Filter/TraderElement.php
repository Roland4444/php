<?php

namespace Application\Form\Filter;

use Reference\Entity\Trader;

/**
 * Class TraderElement
 * @package Application\Form\Filter
 */
class TraderElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'trader',
            'options' => [
                'label' => 'Подразделение:',
                'object_manager' => $this->entityManager,
                'target_class' => Trader::class,
                'property' => 'name',
                'empty_option' => 'Выбрать трейдера',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'value' => isset($params['trader']) ? $params['trader'] : '',
                'class' => 'form-control',
            ]
        ]);
        return $form;
    }
}
