<?php

namespace Kunstmaan\FormBundle\Tests\Entity;

use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-21 at 09:39:33.
 */
class AbstractFormPageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractFormPage
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass('Kunstmaan\FormBundle\Entity\AbstractFormPage');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::setThanks
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::getThanks
     */
    public function testSetGetThanks()
    {
        $object = $this->object;
        $value = 'thanks';
        $object->setThanks($value);
        $this->assertEquals($value, $object->getThanks());
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::setSubject
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::getSubject
     */
    public function testSetGetSubject()
    {
        $object = $this->object;
        $value = 'some subject';
        $object->setSubject($value);
        $this->assertEquals($value, $object->getSubject());
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::setToEmail
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::getToEmail
     */
    public function testSetGetToEmail()
    {
        $object = $this->object;
        $value = 'example@example.com';
        $object->setToEmail($value);
        $this->assertEquals($value, $object->getToEmail());
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::setFromEmail
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::getFromEmail
     */
    public function testSetGetFromEmail()
    {
        $object = $this->object;
        $value = 'example@example.com';
        $object->setFromEmail($value);
        $this->assertEquals($value, $object->getFromEmail());
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertEquals(AbstractFormPageAdminType::class, $this->object->getDefaultAdminType());
    }

    /**
     * @covers \Kunstmaan\FormBundle\Entity\AbstractFormPage::getFormElementsContext
     */
    public function testGetFormElementsContext()
    {
        $stringValue = $this->object->getFormElementsContext();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }
}