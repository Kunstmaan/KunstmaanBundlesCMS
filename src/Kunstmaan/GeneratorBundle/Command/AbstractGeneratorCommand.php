<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Generator\AbstractGenerator;
use Kunstmaan\GeneratorBundle\Helper\QuestionHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @internal
 */
abstract class AbstractGeneratorCommand extends Command
{
    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @var AbstractGenerator
     */
    private $generator;

    // only useful for unit tests
    public function setGenerator(AbstractGenerator $generator)
    {
        $this->generator = $generator;
    }

    abstract protected function createGenerator();

    protected function getGenerator(?BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            $this->generator = $this->createGenerator();
            $this->generator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->generator;
    }

    protected function getSkeletonDirs(?BundleInterface $bundle = null)
    {
        $skeletonDirs = [];

        if (isset($bundle) && is_dir($dir = $bundle->getPath() . '/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getProjectDir() . '/app/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getProjectDir() . '/templates/bundles/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = __DIR__ . '/../Resources/skeleton';
        $skeletonDirs[] = __DIR__ . '/../Resources';

        return $skeletonDirs;
    }

    protected function getQuestionHelper()
    {
        $question = $this->getHelperSet()->get('question');
        if (!$question || get_class($question) !== QuestionHelper::class) {
            $this->getHelperSet()->set($question = new QuestionHelper());
        }

        return $question;
    }

    /**
     * Tries to make a path relative to the project, which prints nicer.
     *
     * @param string $absolutePath
     *
     * @return string
     */
    protected function makePathRelative($absolutePath)
    {
        $projectRootDir = dirname($this->getContainer()->getParameter('kernel.root_dir'));

        return str_replace($projectRootDir . '/', '', realpath($absolutePath) ?: $absolutePath);
    }

    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    protected function getContainer()
    {
        if (null === $this->container) {
            $application = $this->getApplication();
            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved as the application instance is not yet set.');
            }

            $this->container = $application->getKernel()->getContainer();
        }

        return $this->container;
    }
}
