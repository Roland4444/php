<?php

namespace Application\Form\Filter;

use Spare\Entity\OrderStatus;

/**
 * Class SpareOrderStatusElement
 * @package Application\Form\Filter
 */
class SpareOrderStatusElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'orderStatus',
            'options' => [
                'label' => 'Статус заказа:',
                'object_manager' => $this->entityManager,
                'target_class' => OrderStatus::class,
                'property' => 'title',
                'empty_option' => 'Статус заказа',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['id' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['orderStatus'],
                'class' => 'form-control',
            ],
        ]);
        return $form;
    }
}
