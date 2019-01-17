<?php

namespace Kunstmaan\GeneratorBundle\Maker;

use Kunstmaan\GeneratorBundle\Helper\DoctrineHelper;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class MakeDefaultPageparts extends AbstractMaker
{
    /** @var Filesystem */
    private $fs;

    /** @var Environment */
    private $twig;

    /** @var string */
    private $projectDir;

    public function __construct($projectDir)
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
     * Return the command name for your maker (e.g. make:report).
     *
     * @return string
     */
    public static function getCommandName(): string
    {
        return 'make:default-pageparts';
    }

    /**
     * Configure the command: set description, input arguments, options, etc.
     *
     * By default, all arguments will be asked interactively. If you want
     * to avoid that, use the $inputConfig->setArgumentAsNonInteractive() method.
     *
     * @param Command            $command
     * @param InputConfiguration $inputConfig
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Copy the default pageparts')
            ->addOption('prefix', null, InputOption::VALUE_REQUIRED, 'The prefix to be used in the table name of the generated entities', '')
        ;
    }

    /**
     * Configure any library dependencies that your maker requires.
     *
     * @param DependencyBuilder $dependencies
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Environment::class,
            'twig'
        );

        $dependencies->addClassDependency(
            Filesystem::class,
            'filesystem'
        );

        $dependencies->addClassDependency(
            Finder::class,
            'finder'
        );
    }

    /**
     * Called after normal code generation: allows you to do anything.
     *
     * @param InputInterface $input
     * @param ConsoleStyle   $io
     * @param Generator      $generator
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $dbPrefix = '';
        if ($input->getOption('prefix')) {
            $dbPrefix = rtrim($input->getOption('prefix'), '_') . '_';
        }

        $types = [];
        foreach ($this->getAllDefaultPageParts() as $pagePart) {
            $pagePartName = $pagePart->getBasename('.php');

            $pagePartClassDetails = $generator->createClassNameDetails($pagePartName, 'Entity\\PageParts\\');
            $adminTypeClassDetails = $generator->createClassNameDetails($pagePartName . 'AdminType', 'Form\\PageParts\\');

            $this->fs->dumpFile($this->projectDir . '/src/Entity/PageParts/' . $pagePartName . '.php', $this->renderPagePartClass($pagePartClassDetails, $adminTypeClassDetails, $generator->getRootNamespace(), $dbPrefix));

            if ($pagePartName === 'AbstractPagePart') {
                continue;
            }

            $this->fs->dumpFile($this->projectDir . '/src/Form/PageParts/' . $adminTypeClassDetails->getShortName() . '.php', $this->renderAdminTypeClass($pagePartClassDetails, $adminTypeClassDetails, $generator->getRootNamespace()));

            $this->fs->mirror(__DIR__ . '/../Resources/skeleton/default-pageparts/templates/PageParts/' . $pagePartName, $this->projectDir . '/templates/pageparts/' . Str::asSnakeCase(str_replace('PagePart', 'Pagepart', $pagePartName)));

            $types[] = [
                'name' => str_replace('PagePart', '', $pagePartClassDetails->getShortName()),
                'class' => $pagePartClassDetails->getFullName(),
            ];
        }

        $this->renderExtraClasses($generator->getRootNamespace(), $dbPrefix);

        $originalTypes = [];
        $data = Yaml::parse(file_get_contents($this->projectDir . '/config/kunstmaancms/pageparts/main.yml'));
        if (array_key_exists('kunstmaan_page_part', $data)) {
            $originalTypes = $data['kunstmaan_page_part']['pageparts']['main']['types'];
        }

        $types = array_merge($originalTypes, $types);

        $data['kunstmaan_page_part']['pageparts']['main']['types'] = $types;
        $ymlData = Yaml::dump($data, 5);
        file_put_contents($this->projectDir . '/config/kunstmaancms/pageparts/main.yml', $ymlData);
    }

    private function renderPagePartClass(ClassNameDetails $pagePartClassDetails, ClassNameDetails $adminTypeClassDetails, $rootNamespace, $dbPrefix): string
    {
        return $this->twig->render('/default-pageparts/entities/pageparts/' . $pagePartClassDetails->getShortName() . '.php', [
            'namespace' => $rootNamespace,
            'table_name' => $dbPrefix . DoctrineHelper::convertToTableName($pagePartClassDetails->getShortName()),
            'admin_type_class' => $adminTypeClassDetails->getShortName(),
            'admin_type_full' => $adminTypeClassDetails->getFullName(),
        ]);
    }

    private function renderAdminTypeClass(ClassNameDetails $pagePartClassDetails, ClassNameDetails $adminTypeClassDetails, $rootNamespace): string
    {
        return $this->twig->render('/default-pageparts/forms/pageparts/' . $adminTypeClassDetails->getShortName() . '.php', [
            'namespace' => $rootNamespace,
            'pagepart_class' => $pagePartClassDetails->getShortName(),
            'pagepart_class_full' => $pagePartClassDetails->getFullName(),
        ]);
    }

    private function renderExtraClasses($rootNamespace, $dbPrefix)
    {
        $this->fs->dumpFile($this->projectDir . '/src/Entity/UspItem.php', $this->twig->render('/default-pageparts/entities/UspItem.php', [
            'namespace' => $rootNamespace,
            'db_prefix' => $dbPrefix,
        ]));

        $this->fs->dumpFile($this->projectDir . '/src/Form/UspItemAdminType.php', $this->twig->render('/default-pageparts/forms/UspItemAdminType.php', [
            'namespace' => $rootNamespace,
        ]));

        $this->fs->dumpFile($this->projectDir . '/src/Entity/MapItem.php', $this->twig->render('/default-pageparts/entities/MapItem.php', [
            'namespace' => $rootNamespace,
            'db_prefix' => $dbPrefix,
        ]));

        $this->fs->dumpFile($this->projectDir . '/src/Form/MapItemAdminType.php', $this->twig->render('/default-pageparts/forms/MapItemAdminType.php', [
            'namespace' => $rootNamespace,
        ]));

        $this->fs->dumpFile($this->projectDir . '/src/Entity/GalleryRow.php', $this->twig->render('/default-pageparts/entities/GalleryRow.php', [
            'namespace' => $rootNamespace,
            'db_prefix' => $dbPrefix,
        ]));

        $this->fs->dumpFile($this->projectDir . '/src/Form/GalleryRowAdminType.php', $this->twig->render('/default-pageparts/forms/GalleryRowAdminType.php', [
            'namespace' => $rootNamespace,
        ]));

        $this->fs->dumpFile($this->projectDir . '/src/Entity/GalleryRowMediaItem.php', $this->twig->render('/default-pageparts/entities/GalleryRowMediaItem.php', [
            'namespace' => $rootNamespace,
            'db_prefix' => $dbPrefix,
        ]));

        $this->fs->dumpFile($this->projectDir . '/src/Form/GalleryRowMediaItemAdminType.php', $this->twig->render('/default-pageparts/forms/GalleryRowMediaItemAdminType.php', [
            'namespace' => $rootNamespace,
        ]));
    }

    private function getAllDefaultPageParts(): Finder
    {
        $finder = (new Finder())->files()->name('*PagePart.php')->in(__DIR__ . '/../Resources/skeleton/default-pageparts/entities/pageparts')->sortByName();

        return $finder;
    }
}
