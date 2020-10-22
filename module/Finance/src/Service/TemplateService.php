<?php

namespace Finance\Service;

use Application\Service\BaseService;

class TemplateService extends BaseService
{
    protected $entity = '\Finance\Entity\Template';

    public function findAll()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from($this->entity, 'r')
            ->join('r.category', 'c')
            ->orderBy('c.name', 'ASC');
        return $qb->getQuery()->getResult();
    }

    public function findCategoryByOrder($order): ?\Finance\Entity\Template
    {
        if ($order->getDestInn() > 0) {
            $qb = $this->em->createQueryBuilder();
            $qb->select('r')
                ->from($this->entity, 'r')
                ->join('r.category', 'c')
                ->where('r.inn = ' . $order->getDestInn());
            $result = $qb->getQuery()->getResult();
            foreach ($result as $row) {
                if (strpos(strtolower($order->getComment()), strtolower($row->getText())) !== false) {
                    return $row;
                }
            }
        }
        return null;
    }
}
