<?php

namespace Storage\Form;

use Doctrine\ORM\EntityManager;
use Reference\Service\MetalService;
use Storage\Entity\ContainerItem;
use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Element\Hidden;
use DoctrineORMModule\Form\Element\EntitySelect;
use Reference\Entity\Metal;
use Zend\Form\FormInterface;

class ContainerItemForm extends Form
{
    private EntityManager $entityManager;
    private MetalService $metalService;

    public function __construct($entityManager, $metalService)
    {
        $this->entityManager = $entityManager;
        $this->metalService = $metalService;
        parent::__construct('item_form');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($this->entityManager))->setObject(new ContainerItem());
    }

    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ($object->getMetal() === null) {
            $defaultMetal = $this->metalService->findDefault();
            $object->setMetal($defaultMetal);
        }
        return parent::bind($object, $flags);
    }

    public function addElements($isBlack, $showPrice): self
    {
        $this->add([
            'type' => Hidden::class,
            'name' => 'id',
            'attributes' => [
                'id' => 'id',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'type' => EntitySelect::class,
            'name' => 'metal',
            'options' => [
                'label' => 'Металл:',
                'object_manager' => $this->entityManager,
                'target_class' => Metal::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findByGroupType',
                    'params' => [
                        'isFerrous' => $isBlack,
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'id' => 'metal',
                'class' => 'form-control',
            ]
        ]);

        $this->add([
            'name' => 'comment',
            'attributes' => [
                'type' => 'text',
                'id' => 'comment',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Комментарий:',
            ],
        ]);

        $this->add([
            'name' => 'weight',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => true,
                'id' => 'weight',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Отправлено:',
            ],
        ]);

        $this->add([
            'name' => 'realWeight',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'required' => true,
                'id' => 'realWeight',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Принято:',
            ],
        ]);

        if ($showPrice) {
            $this->add([
                'name' => 'cost',
                'attributes' => [
                    'type' => 'number',
                    'step' => '0.01',
                    'min' => '0',
                    'required' => false,
                    'id' => 'cost',
                    'class' => 'form-control',
                ],
                'options' => [
                    'label' => 'Цена:',
                ],
            ]);

            $this->add([
                'name' => 'costDol',
                'attributes' => [
                    'type' => 'number',
                    'step' => '0.001',
                    'min' => '0',
                    'required' => false,
                    'id' => 'costDol',
                    'class' => 'form-control',
                ],
                'options' => [
                    'label' => 'Цена, $:',
                ],
            ]);
        } else {
            $this->add([
                'type' => Hidden::class,
                'name' => 'cost',
                'attributes' => [
                    'value' => '',
                    'id' => 'cost',
                    'class' => 'form-control',
                ]
            ]);
            $this->add([
                'type' => Hidden::class,
                'name' => 'costDol',
                'attributes' => [
                    'value' => '',
                    'id' => 'costDol',
                    'class' => 'form-control',
                ]
            ]);
        }

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Сохранить',
                'id' => 'submit_item',
            ],
        ]);
        return $this;
    }
}
