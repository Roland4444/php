<?php

namespace Finance\Form\Filter;

use Application\Form\Filter\AElement;
use Finance\Entity\BankAccount;

/**
 * Class BankElement
 * @package Finance\Form\Filter
 */
class BankElement extends AElement
{
    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'bank',
            'options' => [
                'label' => 'Банк:',
                'object_manager' => $this->entityManager,
                'target_class' => BankAccount::class,
                'property' => 'name',
                'empty_option' => 'Выбрать счет',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['closed' => 0],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => false,
                'value' => isset($params['bank']) ? $params['bank'] : '',
                'id' => 'bank',
                'class' => 'form-control',
            ]
        ]);
        return $form;
    }
}
