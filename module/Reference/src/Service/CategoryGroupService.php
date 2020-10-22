<?php
namespace Reference\Service;

use Application\Service\BaseService;
use Reference\Entity\CategoryGroup;

/**
 * Class CategoryGroupService
 * @package Reference\Service
 */
class CategoryGroupService extends BaseService
{
    /**
     * CategoryGroupService constructor.
     */
    public function __construct()
    {
        $this->setEntity(CategoryGroup::class);
    }
}
