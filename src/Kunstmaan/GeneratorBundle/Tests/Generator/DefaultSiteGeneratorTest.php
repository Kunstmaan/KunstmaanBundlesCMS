<?php

namespace Kunstmaan\GeneratorBundle\Tests\Generator;

use Kunstmaan\GeneratorBundle\Generator\DefaultSiteGenerator;
use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class DefaultSiteGeneratorTest extends TestCase
{
    public function testGenerator()
    {
        $filesystem = new Filesystem();
        $path = sys_get_temp_dir() . '/' . uniqid();
        $filesystem->remove($path);

        $bundle = $this->getBundle($path);
        $container = $this->createMock('Symfony\Component\DependencyInjection\Container');
        $container
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->will($this->returnValueMap([['multilanguage', true], ['kernel.project_dir', $path]]))
        ;
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with('kunstmaan_admin.multi_language')
            ->will($this->returnValue(true))
        ;

        $generator = new DefaultSiteGenerator($filesystem, $this->getRegistry(), '/defaultsite', $this->getAssistant(), $container);
        $generator->generate($bundle, '', __DIR__ . '/../_data', false);

        $basePath = Kernel::VERSION_ID >= 40000 ? 'templates/bundles/TwigBundle/' : 'app/Resources/TwigBundle/views/';
        unlink(__DIR__ . '/../_data/' . $basePath . 'Exception/error.html.twig');
        unlink(__DIR__ . '/../_data/' . $basePath . 'Exception/error404.html.twig');
        unlink(__DIR__ . '/../_data/' . $basePath . 'Exception/error500.html.twig');
        unlink(__DIR__ . '/../_data/' . $basePath . 'Exception/error503.html.twig');
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
        return $this->createMock('Symfony\Bridge\Doctrine\RegistryInterface');
    }

    protected function getAssistant()
    {
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $commandAssistant = new CommandAssistant();
        $commandAssistant->setOutput($output);

        return $commandAssistant;
    }
}
