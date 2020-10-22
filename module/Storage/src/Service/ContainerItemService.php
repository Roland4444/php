<?php

namespace Storage\Service;

use Core\Service\AbstractService;
use Reference\Service\MetalService;
use Storage\Entity\ContainerItem;
use Storage\Repository\ContainerItemRepository;
use Zend\Form\Form;
use Zend\Validator\Exception\InvalidArgumentException;

class ContainerItemService extends AbstractService
{
    private MetalService $metalService;

    public function __construct($repository, $metalService)
    {
        $this->metalService = $metalService;
        parent::__construct($repository);
    }

    protected function getRepository(): ContainerItemRepository
    {
        return parent::getRepository();
    }

    public function getActualByMetal(string $dateFrom, string $dateTo, $department)
    {
        $weightList = $this->getRepository()->getActualByMetal($dateFrom, $dateTo, $department);
        $result = [];
        foreach ($weightList as $item) {
            $result[$item['id']] = $item['sum'];
        }
        return $result;
    }

    public function getSubtracting($deps)
    {
        $result = [];
        if (count($deps) === 0) {
            return $result;
        }
        $res = $this->getRepository()->getSubtracting($deps);
        if ($res) {
            foreach ($res as $row) {
                $result[$row['id']] = $row['sub'];
            }
        }
        return $result;
    }

    /**
     * @param $postItems
     * @return array
     */
    public function parseItems($postItems): array
    {
        $result = [];
        foreach ($postItems as $postItem) {
            $item = new ContainerItem();

            $form = new Form();
            $form->setInputFilter($item->getInputFilter())
                ->setData($postItem);
            if (! $form->isValid()) {
                throw new InvalidArgumentException('Некорректные данные формы item.');
            }

            $item->setMetal($this->metalService->getReference((int)$postItem['metal']));
            $item->setWeight($postItem['weight']);
            $item->setRealWeight($postItem['realWeight']);
            $item->setComment($postItem['comment']);
            $item->setCost($postItem['cost']);
            $item->setCostDol($postItem['costDol']);
            $result[] = $item;
        }
        return $result;
    }

    public function findByItemId(int $id)
    {
        return $this->getRepository()->findByItemId($id);
    }
}
