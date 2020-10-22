<?php

namespace Spare\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class SellerReturnForm extends Form
{
    private $inputFilter;

    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'startDate',
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
                'name' => 'endDate',
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
                'name' => 'seller',
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
