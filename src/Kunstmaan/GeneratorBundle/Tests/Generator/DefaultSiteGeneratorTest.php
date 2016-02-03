<?php


namespace Kunstmaan\GeneratorBundle\Generator\Tests;


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

        $generator = new DefaultSiteGenerator($filesystem, $this->getRegistry(), '/defaultsite', $this->getAssistant());
        $generator->generate($bundle, '', __DIR__ . '/../data', false);

        $this->assertFileExists($path . '/Twig/NodeTranslationTwigExtension.php');
    }

    protected function getBundle($path)
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
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
        $registry = $this->getMock('Symfony\Bridge\Doctrine\RegistryInterface');

        return $registry;
    }

    protected function getAssistant()
    {
        $output = $this->getMock('Symfony\Component\Console\Output\OutputInterface');

        $commandAssistant = new CommandAssistant();
        $commandAssistant->setOutput($output);

        return $commandAssistant;
    }
}
