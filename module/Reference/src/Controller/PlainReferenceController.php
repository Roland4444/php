<?php

namespace Reference\Controller;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PlainReferenceController extends AbstractActionController
{
    protected $routeIndex;
    protected $service;
    protected $form;
    protected $entityInstance;

    /**
     * Список сущностей
     * @return ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $rows = $this->getService()->findAll();

        return new ViewModel([
            'rows' => $rows,
        ]);
    }

    /**
     * Добавить сущность
     * @return Response|ViewModel
     * @throws \Exception
     */
    public function addAction()
    {
        $form = $this->getForm();
        $form->addElements();
        if (! $this->entityInstance) {
            throw new \Exception('Field "entityInstance" is not specified');
        }
        $form->bind($this->entityInstance);
        $error = '';

        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $form->addNoExistsValidator($postData, $this->entityInstance);
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getService()->save($form->getObject(), $this->getRequest());
                return $this->redirect()->toRoute($this->routeIndex);
            } else {
                $error = '<span id="error-message">Введенные данные некорректны</span>';
            }
        }

        return new ViewModel([
            'form' => $form,
            'action' => 'add',
            'error' => $error,
        ]);
    }

    /**
     * Редактировать сущность
     * @return Response|ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');

        $entity = $this->getService()->find($id);
        $form = $this->getForm();
        $form->addElements();
        $form->bind($entity);

        $this->prepareFormAndEntityForEdit($form, $entity);

        $error = '';
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();
            $form->addNoExistsValidator($postData, $entity);
            $form->setData($postData);
            if ($form->isValid()) {
                $this->getService()->save($entity, $this->getRequest());
                return $this->redirect()->toRoute($this->routeIndex);
            } else {
                $error = '<span id="error-message">Введенные данные некорректны</span>';
            }
        }

        return new ViewModel([
            'form' => $form,
            'action' => 'edit',
            'error' => $error,
            'id' => $id,
        ]);
    }

    /**
     * Удаление сущности
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        $this->getService()->remove($id);
        $this->redirect()->toRoute($this->routeIndex);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getService()
    {
        if (! $this->service) {
            throw new \Exception('Field "service" is not specified');
        }
        return $this->service;
    }

    protected function getForm()
    {
        if (! $this->form) {
            throw new \Exception('Field "service" is not specified');
        }
        return $this->form;
    }

    /**
     * Подготавливает параметры для редактирования
     *
     * @param $form
     * @param $entity
     */
    protected function prepareFormAndEntityForEdit($form, $entity)
    {
    }
}
