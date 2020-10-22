<?php

namespace Modules\Form;

use Zend\Form\Form;

class Payment extends Form
{

    public function prepare()
    {
        echo "
			<script>
				$(document).ready(function(){
                    $('#date').datepicker();
                });
			</script>
		";
    }

    public function __construct()
    {
        parent::__construct('payment');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'date_formal',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
                'class' => 'form-control ui-date',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $this->add([
            'name' => 'money',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Сумма:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'is_paid',
            'options' => [
                'label' => 'Подтвердить',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
