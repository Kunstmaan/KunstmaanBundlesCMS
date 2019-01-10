<?php

namespace Kunstmaan\GeneratorBundle\Maker;

use Kunstmaan\GeneratorBundle\Helper\DoctrineHelper;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class MakeWebsiteSkeleton extends AbstractMaker
{
    /** @var string */
    private $projectDir;

    /** @var Filesystem */
    private $fs;

    /** @var Environment */
    private $twig;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
        $this->fs = new Filesystem();

        $this->twig = new Environment(new FilesystemLoader([__DIR__ . '/../Resources/skeleton']), [
            'debug' => true,
            'cache' => false,
            'strict_variables' => true,
            'autoescape' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getCommandName(): string
    {
        return 'make:website-skeleton';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Create the kunstmaan CMS skeleton')
            ->addOption('browser-sync-url', null, InputOption::VALUE_REQUIRED, 'The URI that will be used for browsersync to connect')
            ->addOption('prefix', null, InputOption::VALUE_REQUIRED, 'The prefix to be used in the table name of the generated entities', '')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $this->copyGroundcontrol($input, $io, $generator);
        $this->copyAssets($input, $io, $generator);

        $this->copyDefaultController();
        $this->copyTemplates($input, $io, $generator);
        $this->copyDefaultPages($input, $generator, ['HomePage', 'ContentPage']);
        $this->copyDefaultSiteFixture($generator);
        $this->copyPageAndPagepartConfigs();

        $generator->writeChanges();
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Filesystem::class,
            'filesystem'
        );

        $dependencies->addClassDependency(
            Finder::class,
            'finder'
        );
    }

    private function copyGroundcontrol(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $mirrorIterator = (new Finder())
            ->files()
            ->in(__DIR__ . '/../Resources/skeleton/website-skeleton/groundcontrol/bin')
            ->filter(function (\SplFileInfo $fileinfo) {
                return !($fileinfo->getRelativePathname() === 'configured-tasks.js');
            })
            ->getIterator();

        $this->fs->mirror(__DIR__ . '/../Resources/skeleton/website-skeleton/groundcontrol', $this->projectDir);
        $this->fs->mirror(__DIR__ . '/../Resources/skeleton/website-skeleton/groundcontrol/bin', $this->projectDir . '/groundcontrol', $mirrorIterator);

        $io->comment(sprintf('<fg=green>%s</>: %s', 'created', 'groundcontrol skeleton'));

        $generator->generateFile($this->projectDir . '/groundcontrol/configured-tasks.js', __DIR__ . '/../Resources/skeleton/website-skeleton/groundcontrol/bin/configured-tasks.js', ['browserSyncUrl' => $input->getOption('browser-sync-url')]);
    }

    private function copyAssets(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $this->fs->mirror(__DIR__ . '/../Resources/skeleton/website-skeleton/assets/', $this->projectDir . '/assets');

        $io->comment(sprintf('<fg=green>%s</>: %s', 'created', 'ui skeleton'));
    }

    private function copyTemplates(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $this->fs->mirror(__DIR__ . '/../Resources/skeleton/website-skeleton/templates/', $this->projectDir . '/templates');

        $io->comment(sprintf('<fg=green>%s</>: %s', 'created', 'template skeleton'));
    }

    private function copyDefaultPages(InputInterface $input, Generator $generator, array $pages)
    {
        $dbPrefix = '';
        if ($input->getOption('prefix')) {
            $dbPrefix = rtrim($input->getOption('prefix'), '_') . '_';
        }

        foreach ($pages as $page) {
            $pageClassDetails = $generator->createClassNameDetails($page, 'Entity\\Pages\\');
            $adminTypeClassDetails = $generator->createClassNameDetails($page . 'AdminType', 'Form\\Pages\\');

            $pageClass = $this->twig->render('/website-skeleton/entities/' . $pageClassDetails->getShortName() . '.php', [
                'namespace' => $generator->getRootNamespace(),
                'table_name' => $dbPrefix . DoctrineHelper::convertToTableName($pageClassDetails->getShortName()),
                'admin_type_class' => $adminTypeClassDetails->getShortName(),
                'admin_type_full' => $adminTypeClassDetails->getFullName(),
            ]);

            $pageAdminType = $this->twig->render('/website-skeleton/form/' . $adminTypeClassDetails->getShortName() . '.php', [
                'namespace' => $generator->getRootNamespace(),
                'pagepart_class' => $pageClassDetails->getShortName(),
                'pagepart_class_full' => $pageClassDetails->getFullName(),
            ]);

            $this->fs->dumpFile($this->projectDir . '/src/Entity/Pages/' . $pageClassDetails->getShortName() . '.php', $pageClass);

            $this->fs->dumpFile($this->projectDir . '/src/Form/Pages/' . $adminTypeClassDetails->getShortName() . '.php', $pageAdminType);

            $this->fs->mirror(__DIR__ . '/../Resources/skeleton/website-skeleton/templates/pages/' . Str::asSnakeCase($pageClassDetails->getShortName()), $this->projectDir . '/templates/pages/' . Str::asSnakeCase($pageClassDetails->getShortName()));
        }
    }

    private function copyDefaultController()
    {
        $this->fs->copy(__DIR__ . '/../Resources/skeleton/website-skeleton/controller/DefaultController.php', $this->projectDir . '/src/Controller/DefaultController.php');
    }

    private function copyDefaultSiteFixture(Generator $generator)
    {
        $fixtureClass = $this->twig->render('/website-skeleton/fixtures/DefaultSiteFixtures.php', [
            'namespace' => $generator->getRootNamespace(),
        ]);

        $this->fs->dumpFile($this->projectDir . '/src/DataFixtures/ORM/DefaultSiteGenerator/DefaultSiteFixtures.php', $fixtureClass);
    }

    private function copyPageAndPagepartConfigs()
    {
        $this->fs->mirror(__DIR__ . '/../Resources/skeleton/website-skeleton/config/', $this->projectDir . '/config/kunstmaancms');
    }
}
