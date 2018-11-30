<?php

namespace Kunstmaan\GeneratorBundle\Tests\Generator;

use Kunstmaan\GeneratorBundle\Generator\DefaultSiteGenerator;
use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Symfony\Component\Filesystem\Filesystem;

class DefaultSiteGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerator()
    {
        $filesystem = new Filesystem();
        $path = sys_get_temp_dir() . '/' . uniqid();
        $filesystem->remove($path);

        $bundle = $this->getBundle($path);
        $container = $this->createMock('Symfony\Component\DependencyInjection\Container');
        $container
            ->expects($this->any())
            ->method('getParameter')
            ->with('multilanguage')
            ->will($this->returnValue(true))
        ;
        $container
            ->expects($this->any())
            ->method('hasParameter')
            ->with('multilanguage')
            ->will($this->returnValue(true))
        ;

        $generator = new DefaultSiteGenerator($filesystem, $this->getRegistry(), '/defaultsite', $this->getAssistant(), $container);
        $generator->generate($bundle, '', __DIR__ . '/../../_data', false);

        unlink(__DIR__ . '/../../_data/app/Resources/TwigBundle/views/Exception/error.html.twig');
        unlink(__DIR__ . '/../../_data/app/Resources/TwigBundle/views/Exception/error404.html.twig');
        unlink(__DIR__ . '/../../_data/app/Resources/TwigBundle/views/Exception/error500.html.twig');
        unlink(__DIR__ . '/../../_data/app/Resources/TwigBundle/views/Exception/error503.html.twig');
    }

    protected function getBundle($path)
    {
        $bundle = $this->createMock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $bundle
            ->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue('Kunstmaan\TestBundle'))
        ;

        $bundle
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('KunstmaanTestBundle'))
        ;

        $bundle
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($path))
        ;

        return $bundle;
    }

    protected function getRegistry()
    {
        $registry = $this->createMock('Symfony\Bridge\Doctrine\RegistryInterface');

        return $registry;
    }

    protected function getAssistant()
    {
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $commandAssistant = new CommandAssistant();
        $commandAssistant->setOutput($output);

        return $commandAssistant;
    }
}
