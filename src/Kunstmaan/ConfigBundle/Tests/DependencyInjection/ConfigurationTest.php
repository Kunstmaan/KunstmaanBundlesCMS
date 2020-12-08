<?php

namespace Kunstmaan\ConfigBundle\Tests\DependencyInjection;

use Kunstmaan\ConfigBundle\DependencyInjection\Configuration;
use Kunstmaan\ConfigBundle\Entity\ConfigurationInterface;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'entities' => [],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }

    public function testConfigDoesntGenerateAsExpected()
    {
        $this->assertPartialConfigurationIsInvalid([['fail']], 'entities');
    }

    public function testConfigUndefinedEntity()
    {
        $this->expectExceptionMessage('Entity "App\\UndefinedEntity" does not exist');
        $this->expectException(\InvalidArgumentException::class);
        $array = [
            'entities' => [
                'App\\UndefinedEntity',
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }

    public function testConfigInvalidEntity()
    {
        $this->expectExceptionMessage('The entity class "Kunstmaan\\ConfigBundle\\Tests\\DependencyInjection\\InvalidConfigEntity" needs to implement the Kunstmaan\\ConfigBundle\\Entity\\ConfigurationInterface');
        $this->expectException(\RuntimeException::class);
        $array = [
            'entities' => [
                InvalidConfigEntity::class,
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }

    public function testConfigValidEntity()
    {
        $array = [
            'entities' => [
                ValidConfigEntity::class,
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);
    }
}

class ValidConfigEntity implements ConfigurationInterface
{
    public function getDefaultAdminType()
    {
        return 'whatever';
    }

    public function getInternalName()
    {
        return 'whatever';
    }

    public function getLabel()
    {
        return 'whatever';
    }

    public function getRoles()
    {
        return [];
    }
}

class InvalidConfigEntity
{
}
