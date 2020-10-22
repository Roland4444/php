<?php

namespace ModuleTest\Form;

use Modules\Form\MoveVehiclesForm;
use PHPUnit\Framework\TestCase;
use ProjectTest\Bootstrap;

class MoveVehiclesFormTest extends TestCase
{
    public function testConstructForm()
    {
        $container = Bootstrap::getServiceManager();
        $entityManager = $container->get('entityManager');
        $form = new MoveVehiclesForm($entityManager);

        $this->assertNotNull($form);
        $this->assertEquals('post', $form->getAttribute('method'));
    }
}
