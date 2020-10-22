<?php

namespace ModuleTest\Form;

use Modules\Form\MoveVehiclesEditForm;
use PHPUnit\Framework\TestCase;
use ProjectTest\Bootstrap;

class MoveVehiclesEditFormTest extends TestCase
{
    public function testConstructFormNotComplete()
    {
        $container = Bootstrap::getServiceManager();
        $entityManager = $container->get('entityManager');
        $form = new MoveVehiclesEditForm($entityManager);

        $this->assertNotNull($form);
        $this->assertEquals('post', $form->getAttribute('method'));
    }
}
