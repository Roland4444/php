<?php

namespace Storage\Form;

use Zend\Form\Form;

class CashIn extends Form
{
    public function __construct()
    {
        parent::__construct('cash_in');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'money',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '1',
                'required' => false,
                'id' => 'money',
                'class' => 'form-control',
                'autocomplete' => 'off'
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
            ],
        ]);
    }
}
