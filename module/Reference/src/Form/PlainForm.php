<?php

namespace Reference\Form;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\NoObjectExists;
use Zend\Form\Form;

abstract class PlainForm extends Form
{
    protected $entityClass;
    protected $entityManager;
    protected $inputFilter;

    /**
     * PlainForm constructor.
     * @param $name
     * @param EntityManager|null $entityManager
     */
    public function __construct($name, EntityManager $entityManager = null)
    {
        $this->entityManager = $entityManager;
        parent::__construct($name);
    }

    public function addNoExistsValidator($postData = null, $entity = null): void
    {
        if ($postData['name'] != $entity->getName()) {
            $this->addNoObjectExistsValidator('name', $this->entityManager->getRepository($this->entityClass));
        }
    }

    abstract public function addElements();

    protected function addNoObjectExistsValidator($field, $objectRepository)
    {
        $this->getInputFilter()->get($field)->getValidatorChain()->attach(
            new NoObjectExists([
                'object_repository' => $objectRepository,
                'fields' => $field,
                'messages' => [
                    NoObjectExists::ERROR_OBJECT_FOUND => 'Объект с именем %value% уже существует',
                ]
            ])
        );
    }
}
