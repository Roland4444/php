<?php

namespace Storage\Form;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Customer;
use Reference\Entity\Metal;
use Reference\Service\CustomerService;
use Reference\Service\MetalService;
use Storage\Entity\Purchase;
use Zend\Form\Form;
use DoctrineORMModule\Form\Element\EntitySelect;
use Zend\Form\FormInterface;

class PurchaseForm extends Form
{
    private EntityManager $entityManager;
    private CustomerService $customerService;
    private MetalService $metalService;

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

    public function __construct($entityManager, $customerService, $metalService)
    {
        parent::__construct('traderReceipts');
        $this->setAttribute('method', 'post');
        $this->entityManager = $entityManager;
        $this->customerService = $customerService;
        $this->metalService = $metalService;
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new Purchase());
    }

    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        $this->addElements($object);
        if ($object->getCustomer() === null) {
            $defaultCustomer = $this->customerService->findDefault();
            $object->setCustomer($defaultCustomer);
        }
        if ($object->getMetal() === null) {
            $defaultMetal = $this->metalService->findDefault();
            $object->setMetal($defaultMetal);
        }
        return parent::bind($object, $flags);
    }

    private function addElements(Purchase $purchase): void
    {
        $this->add([
            'name' => 'date',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $this->add([
            'type' => EntitySelect::class,
            'name' => 'customer',
            'options' => [
                'label' => 'Поставщик:',
                'disable_inarray_validator' => true,
                'object_manager' => $this->entityManager,
                'target_class' => Customer::class,
                'property' => 'name',
                'find_method' => [
                    'name' => 'findUsed',
                    'params' => [
                        'dep' => $purchase->getDepartment()->getId(),
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
                'id' => 'customer',
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
                        'isFerrous' => $purchase->getDepartment()->getType() === 'black',
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
            'name' => 'weight',
            'attributes' => [
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'id' => 'weight',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Масса:',
            ],
        ]);

        $this->add([
            'name' => 'cost',
            'attributes' => [
                'type' => 'number',
                'step' => '0.001',
                'min' => '0',
                'id' => 'cost',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Цена:',
            ],
        ]);

        $this->add([
            'name' => 'formal',
            'attributes' => [
                'type' => 'number',
                'step' => '0.001',
                'min' => '0',
                'required' => false,
                'id' => 'formal',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Офиц. цена:',
            ],
        ]);
    }
}
