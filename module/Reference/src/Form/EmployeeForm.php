<?php

namespace Reference\Form;

use Core\Utils\Options;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Employee;
use Zend\Form\FormInterface;

/**
 * Class EmployeeForm
 * @package Reference\Form
 */
class EmployeeForm extends PlainForm
{
    protected $entityClass = Employee::class;

    /**
     * EmployeeForm constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('user', $entityManager);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new Employee());
    }

    /**
     * {@inheritdoc}
     */
    public function addElements()
    {
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'ФИО сотрудника:',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => Options::OPTIONS_DRIVER,
            'options' => [
                'label' => Options::OPTIONS_DRIVER .':',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);


        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => Options::OPTIONS_SPARE,
            'options' => [
                'label' => Options::OPTIONS_SPARE .':',
                'checked_value' => '1',
                'unchecked_value' => '0'
            ]
        ]);

        $this->add([
            'name' => 'license',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Номер водительских прав:',
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

    /**
     * Собирает форму и наполняет ее данными из сущности
     *
     * @param Employee $object
     * @param int $flags
     * @return \Zend\Form\Form
     */
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        if ($object->isDriver()) {
            $this->get(Options::OPTIONS_DRIVER)->setValue(1);
        }
        if ($object->isSpare()) {
            $this->get(Options::OPTIONS_SPARE)->setValue(1);
        }
        return parent::bind($object, $flags);
    }

    /**
     * Проверка переданной опции водителя и номера водительского удостоверения
     *
     * @return bool
     * @throws \Exception
     */
    public function isValid()
    {
        $license = ! empty($this->getElements()['license']) ? $this->getElements()['license']->value : '';
        $driver = ! empty($this->getElements()['driver']) ? $this->getElements()['driver']->value : '';

        if (! empty($driver) && empty($license)) {
            throw new \Exception('При установке флага ' . Options::OPTIONS_DRIVER
                . ' должен быть указан номер водительских прав');
        }
        return parent::isValid();
    }
}
