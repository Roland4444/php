<?php

namespace Application\Form\Filter;

use Spare\Entity\PlanningStatus;

/**
 * Class SparePlanningStatusElement
 * @package Application\Form\Filter
 */
class SparePlanningStatusElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'planningStatus',
            'options' => [
                'label' => 'Статус:',
                'object_manager' => $this->entityManager,
                'target_class' => PlanningStatus::class,
                'property' => 'title',
                'empty_option' => 'Статус',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['id' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['planningStatus'],
                'class' => 'form-control',
            ],
        ]);
        return $form;
    }
}
