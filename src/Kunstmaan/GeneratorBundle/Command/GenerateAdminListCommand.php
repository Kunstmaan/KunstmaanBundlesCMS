<?php

namespace Kunstmaan\GeneratorBundle\Command;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\DoctrineBundle\Mapping\MetadataFactory;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Sensio\Bundle\GeneratorBundle\Generator;
use Kunstmaan\GeneratorBundle\Generator\AdminListConfigurationGenerator;
use Kunstmaan\GeneratorBundle\Generator\AdminListTypeGenerator;
use Kunstmaan\GeneratorBundle\Generator\AdminListControllerGenerator;

/**
 * Generates a KunstmaanAdminList
 */
class GenerateAdminListCommand extends GenerateDoctrineCommand
{

    private $configurationGenerator;
    private $controllerGenerator;
    private $typeGenerator;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity to create a KunstmaanAdminList for'),))
            ->setDescription('Generates a KunstmaanAdminList')
            ->setHelp(<<<EOT
The <info>kuma:generate:adminlist</info> command generates an AdminList for a Doctrine entity.

<info>php app/console kuma:generate:adminlist Bundle:Entity</info>
EOT
        )
            ->setName('kuma:generate:adminlist');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        foreach (array('entity') as $option) {
            if (null === $input->getOption($option)) {
                throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }

        $entity = $input->getOption('entity');
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $entityClass = $this
            ->getContainer()
            ->get('doctrine')
            ->getEntityNamespace($bundle) . '\\' . $entity;
        $bundle = $this
            ->getApplication()
            ->getKernel()
            ->getBundle($bundle);
        $metadata = $this->getEntityMetadata($entityClass);
        $dialog->writeSection($output, 'AdminList Generation');

        $configurationGenerator = $this->getAdminListConfigurationGenerator();
        $configurationGenerator->generate($bundle, $entity, $metadata[0]);

        $output->writeln('Generating the Configuration code: <info>OK</info>');

        $controllerGenerator = $this->getAdminListControllerGenerator();
        $controllerGenerator->generate($bundle, $entity, $metadata[0]);

        $output->writeln('Generating the Controller code: <info>OK</info>');

        $admintypeGenerator = $this->getAdminListTypeGenerator();
        $admintypeGenerator->generate($bundle, $entity, $metadata[0]);

        $output->writeln('Generating the Type code: <info>OK</info>');

        $dialog->writeSection($output, 'Add the following to your routing.yml');

        $parts = explode('\\', $entity);
        $entity_class = array_pop($parts);

        $output->writeln('/*******************************/');
        $output->writeln('');
        $output->writeln('' . $bundle->getName() . '_' . $entity_class . ':');
        $output->writeln('    resource: "@' . $bundle->getName() . '/Controller/' . $entity_class . 'AdminListController.php"');
        $output->writeln('    type:     annotation');
        $output->writeln('    prefix:   /{_locale}/admin/' . strtolower($entity_class) . '/');
        $output->writeln('    requirements:');
        $output->writeln('         _locale: %requiredlocales%');
        $output->writeln('');
        $output->writeln('/*******************************/');

    }

    protected function getDialogHelper()
    {
        $dialog = $this
            ->getHelperSet()
            ->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this
                ->getHelperSet()
                ->set($dialog = new DialogHelper());
        }
        return $dialog;
    }

    protected function getAdminListConfigurationGenerator()
    {
        if (null === $this->configurationGenerator) {
            $this->configurationGenerator = new AdminListConfigurationGenerator($this
                ->getContainer()
                ->get('filesystem'), __DIR__ . '/../Resources/skeleton/adminlist');
        }
        return $this->configurationGenerator;
    }

    public function setAdminListConfigurationGenerator(AdminListConfigurationGenerator $configurationGenerator)
    {
        $this->configurationGenerator = $configurationGenerator;
    }

    protected function getAdminListControllerGenerator()
    {
        if (null === $this->controllerGenerator) {
            $this->controllerGenerator = new AdminListControllerGenerator($this
                ->getContainer()
                ->get('filesystem'), __DIR__ . '/../Resources/skeleton/controller');
        }
        return $this->controllerGenerator;
    }

    public function setAdminListControllerGenerator(AdminListControllerGenerator $controllerGenerator)
    {
        $this->controllerGenerator = $controllerGenerator;
    }

    protected function getAdminListTypeGenerator()
    {
        if (null === $this->typeGenerator) {
            $this->typeGenerator = new AdminListTypeGenerator($this
                ->getContainer()
                ->get('filesystem'), __DIR__ . '/../Resources/skeleton/form');
        }
        return $this->typeGenerator;
    }

    public function setAdminListTypeGenerator(AdminListTypeGenerator $typeGenerator)
    {
        $this->typeGenerator = $typeGenerator;
    }
}
