<?php

namespace FactoringTest\Form;

use Factoring\Form\AssignmentDebtForm;
use PHPUnit\Framework\TestCase;
use ProjectTest\Bootstrap;
use Zend\Form\Form;

class AssignmentDebtFormTest extends TestCase
{
    /**
     * @var Form
     */
    protected $form;

    protected function setUp(): void
    {
        $container = Bootstrap::getServiceManager();
        $entityManager = $container->get('entityManager');
        $this->form = new AssignmentDebtForm($entityManager);
    }

    public function testPrepare()
    {
        $result = $this->form->prepare();

        $this->assertEquals("
			<script>
			    $(function() { 
                    $('#date').datepicker();
                });
			</script>
		", $result);
    }

    public function testElementsNotEmpty()
    {
        $this->assertNotEmpty($this->form->getElements());
    }
}
