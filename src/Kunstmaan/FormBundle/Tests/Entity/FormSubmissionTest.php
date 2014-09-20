<?php
namespace Kunstmaan\FormBundle\Tests\Entity;
use DateTime;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\NodeBundle\Entity\Node;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-20 at 15:14:43.
 */
class FormSubmissionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormSubmission
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FormSubmission;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::getId
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::setId
     */
    public function testSetGetId()
    {
        $object = $this->object;
        $id = 123;
        $object->setId($id);
        $this->assertEquals($id, $object->getId());
    }


    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::setIpAddress
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::getIpAddress
     */
    public function testSetGetIpAddress()
    {
        $object = $this->object;
        $ip = "127.0.0.1";
        $object->setIpAddress($ip);
        $this->assertEquals($ip, $object->getIpAddress());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::getNode
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::setNode
     */
    public function testSetGetNode()
    {
        $object = $this->object;
        $node = new Node();
        $node->setId(123);
        $object->setNode($node);
        $retrievedNode = $object->getNode();
        $this->assertEquals($node, $retrievedNode);
        $this->assertEquals($node->getId(), $retrievedNode->getId());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::setLang
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::getLang
     */
    public function testSetGetLang()
    {
        $object = $this->object;
        $lang = 'nl';
        $object->setLang($lang);
        $this->assertEquals($lang, $object->getLang());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::setCreated
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::getCreated
     */
    public function testSetGetCreated()
    {
        $object = $this->object;
        $now = new DateTime;
        $object->setCreated($now);
        $this->assertEquals($now, $object->getCreated());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::getFields
     */
    public function testGetFields()
    {
        $object = $this->object;
        $object->getFields();
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmission::__toString
     */
    public function test__toString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }
}
