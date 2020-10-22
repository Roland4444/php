<?php

namespace Storage\Form;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Storage\Entity\PurchaseDeal;
use Zend\Form\Form;

class PurchaseDealForm extends Form
{
    /**
     * PurchaseDealForm constructor.
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct('deal');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new PurchaseDeal());

        $this->add([
            'name' => 'comment',
            'attributes' => [
                'type' => 'text',
                'required' => false,
                'id' => 'comment',
                'class' => 'form-control',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Комментарий к сделке:',
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
