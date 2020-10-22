<?php

namespace Application\Form\Filter;

use Spare\Entity\Seller;

/**
 * Class SpareSellerElement
 * @package Application\Form\Filter
 */
class SpareSellerElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'seller',
            'options' => [
                'label' => 'Поставщик:',
                'object_manager' => $this->entityManager,
                'target_class' => Seller::class,
                'property' => 'name',
                'empty_option' => 'Поставщик',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['seller'],
                'class' => 'form-control',
            ],
        ]);
        return $form;
    }
}
