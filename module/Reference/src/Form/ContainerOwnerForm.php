<?php
namespace Reference\Form;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\ContainerOwner;

/**
 * Class ContainerOwnerForm
 * @package Reference\Form
 */
class ContainerOwnerForm extends PlainForm
{
    protected $entityClass = ContainerOwner::class;
    /**
     * ContainerOwnerForm constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('owner', $entityManager);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new ContainerOwner());
    }

    public function addNoExistsValidator($postData = null, $entity = null): void
    {
        parent::addNoExistsValidator($postData, $entity);
        if ($postData['inn'] != $entity->getInn()) {
            $this->addNoObjectExistsValidator('inn', $this->entityManager->getRepository(ContainerOwner::class));
        }
    }

    public function addElements()
    {
        $this->add([
            'name' => 'name',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Наименование:',
            ],
        ]);

        $this->add([
            'name' => 'inn',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'ИНН:',
            ],
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
