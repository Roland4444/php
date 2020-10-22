<?php

namespace Factoring\Form;

use Core\Utils\Options;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Factoring\Entity\Payment;
use Factoring\Entity\Provider;
use Finance\Entity\BankAccount;
use Reference\Entity\Trader;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterAwareInterface;

/**
 * Class PaymentForm
 * @package Finance\Form
 */
class PaymentForm extends Form implements InputFilterAwareInterface
{
    public function prepare()
    {
        return "
			<script>
			    $(function() { 
                    $('#date').datepicker();
                });
			</script>
		";
    }

    public function __construct($em)
    {
        parent::__construct('factoring_payment');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($em))->setObject(new Payment());

        $this->add([
            'name' => 'date',
            'type' => 'DateTime',
            'attributes' => [
                'id' => 'date',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата:',
                'format' => 'Y-m-d'
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'provider',
            'options' => [
                'label' => 'Банк:',
                'object_manager' => $em,
                'target_class' => Provider::class,
                'property' => 'title',
            ],
            'attributes' => [
                'required' => true,
                'id' => 'provider',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'bank',
            'options' => [
                'label' => 'Счет:',
                'object_manager' => $em,
                'target_class' => BankAccount::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['closed' => 0],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'trader',
            'options' => [
                'label' => 'Контрагент:',
                'object_manager' => $em,
                'target_class' => Trader::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'money',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => true,
                'id' => 'money',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Сумма:',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
