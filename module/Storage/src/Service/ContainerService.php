<?php
namespace Storage\Service;

use Core\Service\AbstractService;
use Reference\Entity\ShipmentTariff;
use Reference\Service\ContainerOwnerService;
use Reference\Service\ShipmentTariffService;
use Storage\Entity\Container;
use Storage\Entity\ContainerExtraOwner;
use Storage\Repository\ContainerRepository;
use Zend\Form\Form;
use Zend\Validator\Exception\InvalidArgumentException;

/**
 * Class ContainerService
 * @method  ContainerRepository getRepository() Метод класса AbstractService
 */
class ContainerService extends AbstractService
{
    private ContainerItemService $itemService;
    private ShipmentTariffService $tariffService;
    private ContainerOwnerService $ownerService;

    public function __construct($repository, $itemService, $tariffService, $ownerService)
    {
        $this->itemService = $itemService;
        $this->tariffService = $tariffService;
        $this->ownerService = $ownerService;
        parent::__construct($repository);
    }

    /**
     * {@inheritdoc}
     */
    public function save($row, $request = null)
    {
        if (! $row->getExtraOwner()->getDateFormal()) {
            $row->getExtraOwner()->setDateFormal($row->date);
        }

        $this->getRepository()->save($row);
    }

    public function findByOwnerAndDates(?int $ownerId, ?string $dateFrom, ?string $dateTo)
    {
        return $this->getRepository()->findByOwnerAndDates($ownerId, $dateFrom, $dateTo);
    }

    private function getDuplicate($post, $shipmentId)
    {
        $result = $this->getRepository()->getDuplicate($post, $shipmentId);
        return $result ? $result[0] : null;
    }

    public function getOwnerCostSumByDate($dateFrom, $dateTo)
    {
        return $this->getRepository()->getOwnerCostSumByDate($dateFrom, $dateTo);
    }

    public function getSumByOwnerFormalOrdered($ownerId = null, $dateFrom = null, $dateTo = null)
    {
        return $this->getRepository()->getSumByOwnerFormalOrdered($ownerId, $dateFrom, $dateTo);
    }

    public function getColorDepartmentsContainersByDate($date)
    {
        return $this->getRepository()->getColorDepartmentsContainersByDate($date);
    }

    public function parseItems(array $postContainers, ?int $shipmentId, ?string $shipmentDate, $postTariff): array
    {
        $containers = [];
        foreach ($postContainers as $postContainer) {
            if ($shipmentId && $conDuplicate = $this->getDuplicate($postContainer, $shipmentId)) {
                $container = $conDuplicate;
            } else {
                $container = new Container();

                $form = new Form();
                $form->setInputFilter($container->getInputFilter())
                    ->setData($postContainer);
                if (! $form->isValid()) {
                    throw new InvalidArgumentException('Некорректные данные формы container.');
                }

                $container->setName($postContainer['name']);
                $ownerEntity = $this->ownerService->find($postContainer['owner']);
                $containerExtraOwner = new ContainerExtraOwner();
                $containerExtraOwner->setOwner($ownerEntity);
                $containerExtraOwner->setContainer($container);

                /** @var ShipmentTariff $tariff */
                $tariff = $this->tariffService->find($postTariff);
                $containerExtraOwner->setOwnerCost($tariff->getMoney());
                $containerExtraOwner->setDateFormal($shipmentDate);
                $containerExtraOwner->setIsPaid(false);
                $container->setExtraOwner($containerExtraOwner);
            }
            if (empty($postContainer['items'])) {
                throw new InvalidArgumentException('Добавьте позиции');
            }
            $items = $this->itemService->parseItems($postContainer['items']);
            $container->addItems($items);

            if ($container->getCountItems()) {
                $containers[] = $container;
            }
        }
        return $containers;
    }

    public function findByItemId(int $id)
    {
        return $this->itemService->findByItemId($id);
    }
}
