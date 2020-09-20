<?php

namespace Kunstmaan\RedirectBundle\Tests\Form;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\RedirectBundle\Form\RedirectAdminType;
use PHPUnit\Framework\TestCase;

class RedirectAdminTypeTest extends TestCase
{
    /**
     * @var RedirectAdminType
     */
    protected $objectMultiDomain;

    /**
     * @var RedirectAdminType
     */
    protected $objectSingleDomain;

    /**
     * @var DomainConfigurationInterface
     */
    protected $multiDomainConfiguration;

    /**
     * @var DomainConfigurationInterface
     */
    protected $singleDomainConfiguration;

    protected function setUp(): void
    {
        $multiDomainConfiguration = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface')
            ->disableOriginalConstructor()->getMock();
        $multiDomainConfiguration->expects($this->any())->method('isMultiDomainHost')->willReturn(true);
        $multiDomainConfiguration->expects($this->any())->method('getHosts')->willReturn(['domain.com', 'domain.be']);

        $singleDomainConfiguration = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface')
            ->disableOriginalConstructor()->getMock();
        $singleDomainConfiguration->expects($this->any())->method('isMultiDomainHost')->willReturn(false);
        $singleDomainConfiguration->expects($this->any())->method('getHosts')->willReturn([]);

        $this->multiDomainConfiguration = $multiDomainConfiguration;
        $this->singleDomainConfiguration = $singleDomainConfiguration;

        $this->objectMultiDomain = new RedirectAdminType();
        $this->objectSingleDomain = new RedirectAdminType();
    }

    public function testBuildForm()
    {
        $builder = $this->createMock('Symfony\Component\Form\Test\FormBuilderInterface');

        $builder
            ->expects($this->at(0))
            ->method('add')
            ->with('domain');
        $builder
            ->expects($this->at(1))
            ->method('add')
            ->with('origin');
        $builder
            ->expects($this->at(2))
            ->method('add')
            ->with('target');
        $builder
            ->expects($this->at(3))
            ->method('add')
            ->with('permanent');

        $this->objectSingleDomain->buildForm($builder, ['domainConfiguration' => $this->singleDomainConfiguration]);

        $builder = $this->createMock('Symfony\Component\Form\Test\FormBuilderInterface');
        $builder
            ->expects($this->at(0))
            ->method('add')
            ->with('domain');
        $builder
            ->expects($this->at(1))
            ->method('add')
            ->with('origin');
        $builder
            ->expects($this->at(2))
            ->method('add')
            ->with('target');
        $builder
            ->expects($this->at(3))
            ->method('add')
            ->with('permanent');

        $this->objectMultiDomain->buildForm($builder, ['domainConfiguration' => $this->multiDomainConfiguration]);
    }
}
