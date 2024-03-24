<?php

namespace Kunstmaan\GeneratorBundle\Tests\Generator;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Kunstmaan\GeneratorBundle\Generator\DefaultSiteGenerator;
use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Util\PhpCompatUtil;
use Symfony\Component\Filesystem\Filesystem;

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
            ->will($this->returnValueMap([['kunstmaan_admin.multi_language', true], ['kernel.project_dir', $path]]))
        ;
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with('kunstmaan_admin.multi_language')
            ->will($this->returnValue(true))
        ;

        $entityPrefixMapping = ['default' => [
            ['App\\Entity', $this->createMock(MappingDriver::class)],
        ]];

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getManagerForClass')
            ->willThrowException(new \ReflectionException())
        ;

        // NEXT_MAJOR: Check can be remove when maker bundle minimum version is updated to 1.44
        $class = new \ReflectionClass(DoctrineHelper::class);
        if ($class->getConstructor()->getNumberOfParameters() > 3) {
            $doctrineHelperArgs = ['App\\Entity', $this->createMock(PhpCompatUtil::class), $registry, true, $entityPrefixMapping];
        } else {
            $doctrineHelperArgs = ['App\\Entity', $registry, $entityPrefixMapping];
        }

        $generator = new DefaultSiteGenerator($filesystem, $this->getRegistry(), '/defaultsite', $this->getAssistant(), $container, new DoctrineHelper(...$doctrineHelperArgs));
        $generator->generate($bundle, '', __DIR__ . '/../_data', false);

        $basePath = 'templates/bundles/TwigBundle/';
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
        return $this->createMock(ManagerRegistry::class);
    }

    protected function getAssistant()
    {
        $output = $this->createMock('Symfony\Component\Console\Output\OutputInterface');

        $commandAssistant = new CommandAssistant();
        $commandAssistant->setOutput($output);

        return $commandAssistant;
    }
}
