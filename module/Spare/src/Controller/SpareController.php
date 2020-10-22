<?php
namespace Spare\Controller;

use Application\Form\Filter\FilterableController;
use Core\Traits\RestMethods;
use Spare\Entity\Spare;
use Spare\Form\SpareForm;
use Spare\Service\SpareService;
use Reference\Controller\AbstractReferenceController;
use Application\Form\Filter\NameElement;
use Application\Form\Filter\SubmitElement;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;

/**
 * Class SpareController
 * @package Spare\Controller
 * @method SpareService getService()
 */
class SpareController extends AbstractReferenceController
{
    use FilterableController, RestMethods;

    private $imageService;
    private $config;

    /**
     * SpareController constructor.
     * @param $container
     */
    public function __construct($container)
    {
        parent::__construct($container, 'spareService', SpareForm::class);
        $this->routeIndex = "sparesReference";
        $this->imageService = $container->get('imageService');
        $config = $container->get('Config');
        $this->config = [
            'uploads_dir' => $config['uploads_dir'],
            'uploads_url' => $config['uploads_url']
        ];
    }

    /**
     * Список сущностей
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $imagePath = $this->config['uploads_url'] . '/spare/';
        return parent::indexAction()->setVariables([
            'permissions' => $this->getPermissions(),
            'imagePath' => $imagePath
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findRowsForIndex(?array $params)
    {
        return $this->getService()->getTableList($params);
    }


    /**
     * Добавить сущность
     * @return \Zend\Http\Response|ViewModel
     */
    public function addAction()
    {
        $entityName = $this->service->getEntity();
        $form = $this->getForm();
        $form->bind($this->createEntityForBind($entityName));
        $error = '';

        if ($this->getRequest()->isPost()) {
            $entityName = $this->service->getEntity();
            $repository = $this->entityManager->getRepository($entityName);
            $form->addNoObjectExistsValidators($this->propertyForExistValidate, $this->getRequest()->getPost(), $repository);

            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                try {
                    $this->service->save($form->getObject(), $this->getRequest());
                    return $this->responseSuccess();
                } catch (\Exception $e) {
                    return $this->responseError($e->getMessage());
                }
            } else {
                $message = '';
                if (! empty($form->getMessages())) {
                    foreach ($form->getMessages() as $key => $msgs) {
                        foreach ($msgs as $msg) {
                            $message .= $key . ' - '. $msg;
                        }
                    }
                }
                return $this->responseError($message);
            }
        }

        return new ViewModel([
            'form' => $form,
            'routeIndex' => $this->routeIndex,
            'action' => 'add',
            'error' => $error,
            'id' => null,
        ]);
    }

    /**
     * Редактировать сущность
     * @return ResponseInterface|ViewModel
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');

        $entity = $this->service->find($id);
        $form = $this->getForm();
        $form->bind($entity);

        $this->prepareDataForEdit($form, $entity);

        $error = '';
        if ($this->getRequest()->isPost()) {
            $entityName = $this->service->getEntity();
            $repository = $this->entityManager->getRepository($entityName);
            $form->addNoObjectExistsValidators($this->propertyForExistValidate, $this->getRequest()->getPost(), $repository, $entity);

            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                try {
                    $this->service->save($form->getData(), $this->getRequest());
                    return $this->responseSuccess();
                } catch (\Exception $e) {
                    return $this->responseError($e->getMessage());
                }
            } else {
                $message = '';
                if (! empty($form->getMessages())) {
                    foreach ($form->getMessages() as $key => $msgs) {
                        foreach ($msgs as $msg) {
                            $message .= $key . ' - '. $msg;
                        }
                    }
                }
                return $this->responseError($message);
            }
        }

        $images = $this->imageService->getImages(get_class($entity), $entity->getId());
        $imagePaths = [];
        foreach ($images as $image) {
            $imagePaths[] = $this->config['uploads_url'] . '/' . $this->imageService->getPath($image);
        }

        return new ViewModel([
            'form' => $form,
            'routeIndex' => $this->routeIndex,
            'action' => 'edit',
            'id' => $id,
            'error' => $error,
            'entity' => json_encode($entity->toArray()),
            'imagePaths' => json_encode($imagePaths)
        ]);
    }

    /**
     * Возвращает форму фильтра
     *
     * @return SubmitElement
     */
    protected function getFilterForm()
    {
        return new SubmitElement(new NameElement($this->entityManager));
    }

    /**
     * Права доступа
     *
     * @return array
     */
    protected function getPermissions()
    {
        return [
            'add' => $this->hasAccess(static::class, 'add'),
            'save' => $this->hasAccess(static::class, 'save'),
            'edit' => $this->hasAccess(static::class, 'edit'),
            'delete' => $this->hasAccess(static::class, 'delete'),
        ];
    }
}
