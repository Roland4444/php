<?php
namespace Reference\Form;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Reference\Entity\Category;
use Reference\Entity\Settings;

/**
 * Class SettingsForm
 * @package Reference\Form
 */
class SettingsForm extends PlainForm
{
    protected $entityClass = Settings::class;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct('settings', $entityManager);
        $this->setAttribute('method', 'post');
        $this->setHydrator(new DoctrineObject($entityManager))->setObject(new Category());
    }

    public function addNoExistsValidator($postData = null, $entity = null): void
    {
        if ($postData['label'] != $entity->getLabel()) {
            $this->addNoObjectExistsValidator('label', $this->entityManager->getRepository($this->entityClass));
        }
    }
    /**
     * @return mixed|void
     */
    public function addElements()
    {
        $this->add([
            'name' => 'label',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Название:',
            ],
        ]);
        $this->add([
            'name' => 'alias',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Алиас:',
            ],
        ]);
        $this->add([
            'name' => 'value',
            'attributes' => [
                'type' => 'text',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Значение:',
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
