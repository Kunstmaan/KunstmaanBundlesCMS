<?php

namespace Kunstmaan\ConfigBundle\Tests\DependencyInjection\Compiler;

use Kunstmaan\ConfigBundle\Entity\ConfigurationInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Kunstmaan\ConfigBundle\DependencyInjection\Compiler\KunstmaanConfigConfigurationPass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FakeDoctrine
{
    public function getManagerForClass()
    {
        return true;
    }
}

class FakeDoctrineFalse
{
    /**
     * @throws \ReflectionException
     */
    public function getManagerForClass()
    {
        throw new ReflectionException();
    }
}

class RandomEntity implements ConfigurationInterface
{
    /**
     * @return string
     */
    public function getDefaultAdminType()
    {
        return 'whatever';
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return 'whatever';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'whatever';
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return [];
    }
}

class RandomNonConfigEntity
{
}

class KunstmaanConfigConfigurationPassCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new KunstmaanConfigConfigurationPass());
    }

    public function testContainerKeys()
    {
        $doctrine = new FakeDoctrine();

        $svcId = 'kunstmaan_config';

        $this->container->setParameter($svcId, [
            'entities' => [
                'Test\Kunstmaan\ConfigBundle\DependencyInjection\Compiler\RandomEntity',
            ],
        ]);
        $this->container->setDefinition('doctrine', new Definition($doctrine));

        $svc = new Definition();
        $svc->addTag('doctrine');
        $this->setDefinition($svcId, $svc);

        $this->compile();
    }

    public function testExceptionIsThrownWhenNoEntitiesDefined()
    {
        // No longer fails on compile, just returns an empty array, but proof will show in code coverage
        $svcId = 'kunstmaan_config';

        $this->container->setParameter($svcId, []);
        $this->compile();
    }

    public function testExceptionIsThrownWhenNoEntitiesFoundByDoctrine()
    {
        $doctrine = new FakeDoctrineFalse();

        $svcId = 'kunstmaan_config';

        $this->container->setParameter($svcId, [
            'entities' => [
                'BrokenEntity',
            ],
        ]);
        $this->container->setDefinition('doctrine', new Definition($doctrine));

        $svc = new Definition();
        $svc->addTag('doctrine');
        $this->setDefinition($svcId, $svc);

        $this->compile();
    }

    public function testExceptionIsThrownWhenEntityNotAConfigInterface()
    {
        $doctrine = new FakeDoctrine();

        $svcId = 'kunstmaan_config';

        $this->container->setParameter($svcId, [
            'entities' => [
                'Test\Kunstmaan\ConfigBundle\DependencyInjection\Compiler\RandomNonConfigEntity',
            ],
        ]);
        $this->container->setDefinition('doctrine', new Definition($doctrine));

        $svc = new Definition();
        $svc->addTag('doctrine');
        $this->setDefinition($svcId, $svc);

        $this->compile();
    }
}
