<?php

namespace Application\Form\Filter;

use Spare\Entity\Spare;

/**
 * Class SpareElement
 * @package Application\Form\Filter
 */
class SpareElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'spare',
            'options' => [
                'label' => 'Запчасть:',
                'object_manager' => $this->entityManager,
                'target_class' => Spare::class,
                'property' => 'name',
                'empty_option' => 'Запчасть',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['spare'],
                'class' => 'form-control s2',
            ],
        ]);
        return $form;
    }
}
