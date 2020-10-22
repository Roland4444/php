<?php

namespace Application\Form\Filter;

use Spare\Entity\OrderPaymentStatus;

/**
 * Class PaymentStatusElement
 * @package Application\Form\Filter
 */
class PaymentStatusElement extends AElement
{
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'paymentStatus',
            'options' => [
                'label' => 'Статус оплаты:',
                'object_manager' => $this->entityManager,
                'target_class' => OrderPaymentStatus::class,
                'property' => 'title',
                'empty_option' => 'Статус оплаты',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['id' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'value' => $params['paymentStatus'],
                'class' => 'form-control',
            ],
        ]);
        return $form;
    }
}
