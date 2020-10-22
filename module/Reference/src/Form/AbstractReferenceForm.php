<?php
namespace Reference\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use DoctrineModule\Validator\NoObjectExists;

/**
 * Class AbstractReferenceForm
 * @package Reference\Form
 */
abstract class AbstractReferenceForm extends Form
{

    protected $inputFilter;

    /** @var EntityManager */
    protected $entityManager;

    /**
     * AbstractReferenceForm constructor.
     * @param int|null|string $name
     * @param array $entityManager
     */
    public function __construct($name, $entityManager = null)
    {
        $this->entityManager = $entityManager;
        parent::__construct($name);
    }

    /**
     * Prepare form
     * @return void|Form
     */
    public function prepare()
    {
    }

    /**
     * Валидатор проверяет нет ли уже сущности с таким значением
     * @param $field
     * @param $objectRepository
     */
    public function addNoObjectExistsValidator($field, $objectRepository)
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

    /**
     * Добавление валидаторов NoObjectExists для указанных полей
     *
     * @param array     $properties Поля, которые нужно валидировать
     * @param iterable  $postData
     * @param           $repository
     * @param null      $entity
     */
    public function addNoObjectExistsValidators($properties, iterable $postData, $repository, $entity = null)
    {
        foreach ($properties as $value) {
            if (! empty($entity)) {
                $method = 'get' . $value;
                if (method_exists($entity, $method) && $postData[$value] == $entity->$method()) {
                    continue;
                }
            }

            if (! empty($postData[$value])) {
                $this->addNoObjectExistsValidator($value, $repository);
            }
        }
    }

    /**
     * Добавление элементов в форму
     * @return mixed
     */
    abstract public function addElements();
}
