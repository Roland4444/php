<?php

namespace Reports\Form;

use Application\Form\Filter\BaseFilterForm;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Regex;

class DepExportFilter extends Form
{
    use BaseFilterForm;

    private $inputFilter;

    public function prepare()
    {
        echo "
			<script>
				$(document).ready(function(){
					$('#startdate').datepicker({showOtherMonths: true, selectOtherMonths: true}).css('width', '150px');
					$('#enddate').datepicker({showOtherMonths: true, selectOtherMonths: true}).css('width', '150px');
				});
			</script>
		";
    }

    public function __construct($sm, $params)
    {
        parent::__construct('filter');
        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'DepExportFilter');

        $em = $sm->get('Doctrine\ORM\EntityManager');

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'options' => [
                'value_options' => [
                    '1' => 'Экспорт',
                    '2' => 'Переброска'
                ],
            ],
            'attributes' => [
                'value' => isset($params['type']) ? $params['type'] : '1',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'startdate',
            'type' => 'Text',
            'attributes' => [
                'id' => 'startdate',
                'autocomplete' => 'off',
                'placeholder' => 'Начало периода',
                'value' => isset($params['startdate']) ? $params['startdate'] : date('Y-m-d'),
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'enddate',
            'type' => 'Text',
            'attributes' => [
                'id' => 'enddate',
                'autocomplete' => 'off',
                'placeholder' => 'Конец периода',
                'value' => isset($params['enddate']) ? $params['enddate'] : date('Y-m-d'),
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\EntitySelect',
            'name' => 'department',
            'options' => [
                'object_manager' => $em,
                'target_class' => 'Reference\Entity\Department',
                'property' => 'name',
                'empty_option' => 'Все подразделения',
                'find_method' => [
                    'name' => 'findOpened',
                    'params' => [
                        'isAdmin' => false,
                    ],
                ],
            ],
            'attributes' => [
                'value' => isset($params['department']) ? $params['department'] : '',
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'name' => 'comment',
            'attributes' => [
                'type' => 'text',
                'id' => 'comment',
                'value' => $params['comment'],
                'placeholder' => 'Номер',
                'class' => 'form-control',
            ],
            'options' => [
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'class' => 'btn btn-default',
                'value' => 'Фильтр',
                'id' => 'submitbutton',
            ],
        ]);
    }

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'startdate',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'date',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'enddate',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'date',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'comment',
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 300,
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[а-яА-Яa-zA-Z0-9. ()\",\-%\'\/№;_|:\"%№@"]{1,300}$/iu',
                            'messages' => [
                                Regex::INVALID => 'Invalid input, only 0-9 . characters allowed',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'department',
                'required' => false,
                'validators' => [
                    [
                        'name' => 'int',
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'type',
                'required' => false,
                'validators' => [
                    [
                        'name' => 'int',
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
