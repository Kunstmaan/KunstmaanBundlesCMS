<?php

namespace Kunstmaan\RedirectBundle\Tests\Form;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Kunstmaan\RedirectBundle\Form\RedirectAdminType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Test\TypeTestCase;

class RedirectAdminTypeTest extends TypeTestCase
{
    /**
     * @dataProvider domainFormFieldProvider
     * @param MockObject|DomainConfigurationInterface
     */
    public function testDomainFormField(string $expectedFormType, MockObject $domainConfig)
    {
        $form = $this->factory->create(RedirectAdminType::class, null, [
            'domainConfiguration' => $domainConfig,
        ]);

        $this->assertInstanceOf(
            $expectedFormType,
            $form->get('domain')->getConfig()->getType()->getInnerType()
        );
    }

    /**
     * @dataProvider pathNormalizerProvider
     */
    public function testRedirectPathNormalizer(array $formData, Redirect $expectedData)
    {
        $domainConfig = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();
        $domainConfig->method('isMultiDomainHost')->willReturn(false);
        $domainConfig->method('getHosts')->willReturn([]);

        $model = new Redirect();
        $form = $this->factory->create(RedirectAdminType::class, $model, ['domainConfiguration' => $domainConfig]);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertSame($expectedData->getOrigin(), $model->getOrigin());
        $this->assertSame($expectedData->getTarget(), $model->getTarget());
    }

    public function domainFormFieldProvider(): iterable
    {
        $multiDomainConfiguration = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();
        $multiDomainConfiguration->method('isMultiDomainHost')->willReturn(true);
        $multiDomainConfiguration->method('getHosts')->willReturn(['domain.com', 'domain.be']);

        $singleDomainConfiguration = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();
        $singleDomainConfiguration->method('isMultiDomainHost')->willReturn(false);
        $singleDomainConfiguration->method('getHosts')->willReturn([]);

        yield 'Single domain' => [HiddenType::class, $singleDomainConfiguration];
        yield 'Multi domain' => [ChoiceType::class, $multiDomainConfiguration];
    }

    public function pathNormalizerProvider(): iterable
    {
        yield 'Path without prefix slash' => [
            ['origin' => 'origin1', 'target' => 'target1'], (new Redirect())->setOrigin('/origin1')->setTarget('/target1'),
        ];
        yield 'Path with prefix slash' => [
            ['origin' => '/origin1', 'target' => '/target1'], (new Redirect())->setOrigin('/origin1')->setTarget('/target1'),
        ];
        yield 'Path without prefix slash and external url target' => [
            ['origin' => '/origin1', 'target' => 'https://www.google.com'], (new Redirect())->setOrigin('/origin1')->setTarget('https://www.google.com'),
        ];
    }
}
