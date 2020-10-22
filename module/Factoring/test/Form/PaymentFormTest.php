<?php

namespace FactoringTest\Form;

use Factoring\Form\PaymentForm;
use PHPUnit\Framework\TestCase;
use ProjectTest\Bootstrap;
use Zend\Form\Form;

class PaymentFormTest extends TestCase
{
    /**
     * @var Form
     */
    protected $form;

    protected function setUp(): void
    {
        $container = Bootstrap::getServiceManager();
        $entityManager = $container->get('entityManager');
        $this->form = new PaymentForm($entityManager);
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
