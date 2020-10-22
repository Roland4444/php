<?php
namespace Spare\Service;

use Application\Service\BaseService;
use Spare\Entity\Seller;

class SellerService extends BaseService
{
    protected $order = ['id' => 'ASC'];

    public function __construct()
    {
        $this->setEntity(Seller::class);
    }

    public function getTableList($params)
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('s')
            ->from($this->entity, 's')
            ->orderBy('s.name', 'ASC');

        if (! empty($params['seller'])) {
            $qb->where('s.id = :id')
                ->setParameter('id', $params['seller']);
        }
        if (! empty($params['inn'])) {
            $qb->andWhere('s.id = :id')
                ->setParameter('id', $params['inn']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getInnsBySellers(array $sellers): array
    {
        $result = [];
        foreach ($sellers as $seller) {
            $result[] = $seller->getInn();
        }
        return $result;
    }

    public function getByInn(array $sellers, string $inn): ?Seller
    {
        foreach ($sellers as $seller) {
            if ($seller->getInn() === $inn) {
                return $seller;
            }
        }
        return null;
    }
}
