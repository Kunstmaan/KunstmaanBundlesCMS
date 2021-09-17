<?php

namespace Kunstmaan\RedirectBundle\Tests\DependencyInjection;

use Kunstmaan\RedirectBundle\DependencyInjection\Configuration;
use Kunstmaan\RedirectBundle\Entity\Redirect;
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

    public function testProcessedValueContainsRequiredValue()
    {
        $array = [];

        $this->assertProcessedConfigurationEquals([$array], ['redirect_entity' => Redirect::class]);
    }
}
