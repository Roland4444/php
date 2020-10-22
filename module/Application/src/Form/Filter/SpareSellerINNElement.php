<?php

namespace Application\Form\Filter;

use Spare\Entity\Seller;
use Zend\Form\Form;

class SpareSellerINNElement implements IElement
{
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = new Form('filter');

        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'inn',
            'options' => [
                'label' => 'ИНН:',
                'object_manager' => $this->entityManager,
                'target_class' => Seller::class,
                'property' => 'inn',
                'empty_option' => 'ИНН',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['inn' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['inn'],
                'class' => 'form-control',
                'id' => 'inn'
            ],
        ]);
        return $form;
    }
}
