<?php
namespace Reference\Service;

use Application\Service\BaseService;
use Reference\Entity\ContainerOwner;

class ContainerOwnerService extends BaseService
{
    /**
     * ContainerOwnerService constructor.
     */
    public function __construct()
    {
        $this->setEntity(ContainerOwner::class);
    }
}
