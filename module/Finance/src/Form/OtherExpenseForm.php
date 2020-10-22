<?php

namespace Finance\Form;

use Application\Service\AccessService;
use Doctrine\ORM\EntityManager;
use Finance\Controller\OfficeOtherExpenseController;
use Finance\Entity\BankAccount;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Form;
use Finance\Entity\OtherExpense as Expense;

class OtherExpenseForm extends Form
{
    private const MIN_EDIT_DATE = '-1 day'; //Данные для ограничения редактирования по дате

    /**
     * @var AccessService
     */
    private $accessService;

    /**
     * OtherExpense constructor.
     * @param EntityManager $entityManager
     * @param AccessService $accessService
     * @throws
     */
    public function __construct($entityManager, $accessService)
    {
        $this->accessService = $accessService;

        parent::__construct('OtherExpenseForm');
        $this->setAttribute('method', 'post');

        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new Expense());

        $this->add([
            'name' => 'recipient',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Получатель:',
            ],
        ]);

        $this->add([
            'name' => 'date',
            'attributes' => [
                'type' => 'text',
                'id' => 'date',
                'class' => 'form-control ui-date',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Дата:',
            ],
        ]);

        $this->add([
            'name' => 'inn',
            'attributes' => [
                'type' => 'text',
                'required' => false,
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'ИНН:',
            ],
        ]);

        $this->add([
            'name' => 'comment',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Комментарий:',
            ],
        ]);

        $this->add([
            'name' => 'realdate',
            'attributes' => [
                'type' => 'text',
                'id' => 'realdate',
                'class' => 'form-control ui-date',
                'autocomplete' => 'off'
            ],
            'options' => [
                'label' => 'Дата расхода:',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\DoctrineEntity',
            'name' => 'category',
            'options' => [
                'label' => 'Категория:',
                'object_manager' => $entityManager,
                'target_class' => 'Reference\Entity\Category',
                'property' => 'name',
                'find_method' => [
                    'name' => 'findNotArchival',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control s2',
            ]
        ]);

        $this->add([
            'type' => 'DoctrineORMModule\Form\Element\DoctrineEntity',
            'name' => 'bank',
            'options' => [
                'label' => 'Счет:',
                'object_manager' => $entityManager,
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
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isValid()
    {
        $hasFullAccessEdit = $this->accessService->isAllowed(OfficeOtherExpenseController::class, 'edit');

        if (! $hasFullAccessEdit) {
            $dateRow = $this->getObject()->getDate();
            $date = $this->get('date')->value;

            $minDate = new \DateTime();
            $minDate->modify(self::MIN_EDIT_DATE);
            $minDate->setTime(0, 0);

            $dateTime = new \DateTime($date);
            $dateTimeRow = new \DateTime($dateRow);

            if ($minDate > $dateTimeRow || $minDate > $dateTime) {
                $this->setMessages(['date' => ['Нет доступа на редактирование данной даты']]);
                return false;
            }
        }

        return parent::isValid();
    }
}
