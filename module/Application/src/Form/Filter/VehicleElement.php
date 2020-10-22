<?php

namespace Application\Form\Filter;

/**
 * Class VehicleElement
 * @package Application\Form\Filter
 */
class VehicleElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'vehicle',
            'options' => [
                'label' => 'Техника:',
                'object_manager' => $this->entityManager,
                'target_class' => 'Reference\Entity\Vehicle',
                'property' => 'name',
                'empty_option' => 'Техника',
                'find_method' => [
                    'name' => 'findMovable',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['vehicle'],
                'class' => 'form-control',
            ],
        ]);
        return $form;
    }
}
