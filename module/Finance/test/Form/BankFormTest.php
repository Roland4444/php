<?php

namespace ProjectTest\Form;

use Doctrine\ORM\EntityManager;
use Finance\Form\BankForm;
use PHPUnit\Framework\TestCase;
use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class BankFormTest extends TestCase
{
    /**
     * @var BankForm
     */
    private $form;

    public function setUp(): void
    {
        $mockedEntityManager = $this->createMock(EntityManager::class);
        $this->form = new BankForm($mockedEntityManager);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(BankForm::class, $this->form);
        $this->assertEquals('bank', $this->form->getName());
        $this->assertEquals('post', $this->form->getAttribute('method'));
    }

    public function testAddElements()
    {
        $this->assertEquals(0, count($this->form->getElements()));
        $this->form->addElements();
        $this->assertEquals(8, count($this->form->getElements()));
        $this->assertInstanceOf(Element::class, $this->form->get('name'));
        $this->assertInstanceOf(Element::class, $this->form->get('bank'));
        $this->assertInstanceOf(Element::class, $this->form->get('cash'));
        $this->assertInstanceOf(Element::class, $this->form->get('def'));
        $this->assertInstanceOf(Element::class, $this->form->get('closed'));
        $this->assertInstanceOf(Element::class, $this->form->get('submit'));
    }

    public function testGetInputFilter()
    {
        $inputFilter = $this->form->getInputFilter();
        $this->assertInstanceOf(InputFilter::class, $inputFilter);
        $this->assertInstanceOf(Input::class, $inputFilter->get('name'));
        $this->assertInstanceOf(Input::class, $inputFilter->get('bank'));
        $this->assertInstanceOf(Input::class, $inputFilter->get('cash'));
        $this->assertInstanceOf(Input::class, $inputFilter->get('def'));
        $this->assertInstanceOf(Input::class, $inputFilter->get('closed'));
    }
}
