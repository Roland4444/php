<?php

namespace ModuleTest\Form;

use Modules\Form\MoveVehiclesCompleteForm;
use ProjectTest\Bootstrap;
use PHPUnit\Framework\TestCase;

class MoveVehiclesCompleteFormTest extends TestCase
{
    public function testConstructForm()
    {
        $container = Bootstrap::getServiceManager();
        $entityManager = $container->get('entityManager');
        $form = new MoveVehiclesCompleteForm($entityManager);

        $this->assertNotNull($form);
        $this->assertEquals('post', $form->getAttribute('method'));
    }
}
