<?php

namespace Kunstmaan\RedirectBundle\Tests\Entity;

use Kunstmaan\RedirectBundle\Entity\Redirect;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

class RedirectTest extends TestCase
{
    /**
     * @var Redirect
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Redirect();
    }

    public function testGetSetDomain()
    {
        $this->object->setDomain('domain.com');
        $this->assertEquals('domain.com', $this->object->getDomain());
    }

    public function testGetSetOrigin()
    {
        $this->object->setOrigin('origin');
        $this->assertEquals('origin', $this->object->getOrigin());
    }

    public function testGetSetTarget()
    {
        $this->object->setTarget('target');
        $this->assertEquals('target', $this->object->getTarget());
    }

    public function testGetSetPermanent()
    {
        $this->object->setPermanent(true);
        $this->assertTrue($this->object->isPermanent());
    }

    /**
     *  Shouldn't this validation method go in an entity validator class?
     */
    public function testValidateEntityCallsContext()
    {
        $violationBuilder = $this->getMockBuilder(ConstraintViolationBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $violationBuilder->expects($this->once())
            ->method('atPath')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($violationBuilder);

        $this->object->setOrigin('riches');
        $this->object->setTarget('riches');
        $this->object->validate($context);
    }

    public function testValidateEntityDoesntCallContext()
    {
        $violationBuilder = $this->getMockBuilder(ConstraintViolationBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $violationBuilder->expects($this->never())
            ->method('atPath')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->never())
            ->method('addViolation');

        $context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->never())
            ->method('buildViolation')
            ->willReturn($violationBuilder);

        $this->object->setOrigin('rags');
        $this->object->setTarget('riches');
        $this->object->validate($context);
    }
}
