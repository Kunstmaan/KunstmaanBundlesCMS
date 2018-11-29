<?php

namespace Kunstmaan\AdminBundle\Tests\unit;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

abstract class AbstractPrependableExtensionTestCase extends AbstractExtensionTestCase
{
    /**
     * Call this method from within your test after you have (optionally) modified the ContainerBuilder for this test
     * ($this->container).
     *
     * @param array $specificConfiguration
     */
    protected function load(array $configurationValues = array())
    {
        $configs = array($this->getMinimalConfiguration(), $configurationValues);

        foreach ($this->container->getExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($this->container);
            }
            $extension->load($configs, $this->container);
        }
    }
}
