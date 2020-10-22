<?php

namespace Application\Form\Filter;

use Reference\Entity\Vehicle;

/**
 * Class VehicleAll
 * @package Application\Form\Filter
 */
class VehicleAllElement extends AElement
{
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'vehicle',
            'options' => [
                'label' => 'Техника:',
                'object_manager' => $this->entityManager,
                'target_class' => Vehicle::class,
                'property' => 'name',
                'empty_option' => 'Техника',
                'find_method' => [
                    'name' => 'findNotArchival',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['id' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['vehicle'],
                'class' => 'form-control s2',
            ],
        ]);
        return $form;
    }
}
