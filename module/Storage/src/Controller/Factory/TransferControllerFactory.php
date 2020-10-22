<?php

namespace Storage\Controller\Factory;

use Core\Controller\Factory\CrudControllerFactory;
use Storage\Service\TransferService;

/**
 * Class TransferControllerFactory
 * @package Storage\Controller\Factory
 */
class TransferControllerFactory extends CrudControllerFactory
{
    protected $service = TransferService::class;
}
