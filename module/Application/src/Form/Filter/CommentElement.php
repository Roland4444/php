<?php
namespace Application\Form\Filter;

/**
 * Class CommentElement
 * @package Application\Form\Filter
 */
class CommentElement extends AElement
{

    /**
     * {@inheritdoc}
     */
    public function getForm(array $params)
    {
        $form = $this->element->getForm($params);
        $form->add([
            'name' => 'comment',
            'attributes' => [
                'type' => 'text',
                'value' => isset($params['comment']) ? $params['comment'] : '',
                'placeholder' => 'Комментарий',
                'class' => 'form-control',
                'id' => 'comment'
            ],
            'options' => [
                'label' => 'Комментарий:',
            ],
        ]);
        return $form;
    }
}
