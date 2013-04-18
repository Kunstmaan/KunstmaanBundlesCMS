<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadata;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates all classes for an admin list
 */
class AdminListGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $skeletonDir;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var DialogHelper
     */
    private $dialog;

    /**
     * @param Filesystem      $filesystem  The filesystem
     * @param string          $skeletonDir The directory of the skeleton
     * @param OutputInterface $output      The output
     * @param DialogHelper    $dialog      The dialog
     */
    public function __construct(Filesystem $filesystem, $skeletonDir, OutputInterface $output, DialogHelper $dialog)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
        $this->output = $output;
        $this->dialog = $dialog;
    }

    /**
     * @param Bundle        $bundle            The bundle
     * @param string        $entity            The entity name
     * @param ClassMetadata $metadata          The meta data
     * @param boolean       $generateAdminType True if we need to specify the admin type
     *
     * @throws \RuntimeException
     * @return void
     */
    public function generate(Bundle $bundle, $entity, ClassMetadata $metadata)
    {
        $parts = explode('\\', $entity);
        $entityName = array_pop($parts);
        $generateAdminType = !method_exists($entity, 'getAdminType');

        if ($generateAdminType) {
            try {
                $this->generateAdminType($bundle, $entityName, $metadata);
            } catch (\Exception $error) {
                $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
            }
            $this->output->writeln('Generating the Type code: <info>OK</info>');
        }

        try {
            $this->generateConfiguration($bundle, $entityName, $metadata, $generateAdminType);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        $this->output->writeln('Generating the Configuration code: <info>OK</info>');

        try {
            $this->generateController($bundle, $entityName, $metadata);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        $this->output->writeln('Generating the Controller code: <info>OK</info>');
    }

    /**
     * @param Bundle        $bundle            The bundle
     * @param string        $entityName        The entity name
     * @param ClassMetadata $metadata          The meta data
     * @param boolean       $generateAdminType True if we need to specify the admin type
     *
     * @throws \RuntimeException
     * @return void
     */
    public function generateConfiguration(Bundle $bundle, $entityName, ClassMetadata $metadata, $generateAdminType)
    {
        $className = sprintf("%sAdminListConfigurator", $entityName);
        $dirPath = sprintf("%s/AdminList", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));

        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }

        $this->renderFile($this->skeletonDir . '/AdminList', 'AdminListConfigurator.php', $classPath, array(
            'namespace'           => $bundle->getNamespace(),
            'bundle'              => $bundle,
            'entity_class'        => $entityName,
            'fields'              => $this->getFieldsWithFilterTypeFromMetadata($metadata),
            'generate_admin_type' => $generateAdminType
        ));
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param string $entityName The entity name
     *
     * @throws \RuntimeException
     */
    public function generateController(Bundle $bundle, $entityName)
    {
        $className = sprintf("%sAdminListController", $entityName);
        $dirPath = sprintf("%s/Controller", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));

        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }

        $this->renderFile($this->skeletonDir . '/Controller', 'EntityAdminListController.php', $classPath, array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'entity_class'      => $entityName,
        ));

    }

    /**
     * @param Bundle        $bundle   The bundle
     * @param string        $entity   The entity name
     * @param ClassMetadata $metadata The meta data
     *
     * @throws \RuntimeException
     */
    public function generateAdminType(Bundle $bundle, $entityName, ClassMetadata $metadata)
    {
        $className = sprintf("%sAdminType", $entityName);
        $dirPath = sprintf("%s/Form", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));

        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }

        $this->renderFile($this->skeletonDir . '/Form', 'EntityAdminType.php', $classPath, array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'entity_class'      => $entityName,
            'fields'            => $this->getFieldsFromMetadata($metadata)
        ));
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return string[]
     */
    private function getFieldsFromMetadata(ClassMetadata $metadata)
    {
        return GeneratorUtils::getFieldsFromMetadata($metadata);
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return array
     */
    private function getFieldsWithFilterTypeFromMetadata(ClassMetadata $metadata)
    {
        $mapping = array(
            'string' => 'ORM\StringFilterType',
            'text' => 'ORM\StringFilterType',
            'integer' => 'ORM\NumberFilterType',
            'smallint' => 'ORM\NumberFilterType',
            'bigint' => 'ORM\NumberFilterType',
            'decimal' => 'ORM\NumberFilterType',
            'boolean' => 'ORM\BooleanFilterType',
            'date' => 'ORM\DateFilterType',
            'datetime' => 'ORM\DateFilterType',
            'time' => 'ORM\DateFilterType'
        );

        $fields = array();

        foreach ($this->getFieldsFromMetadata($metadata) as $fieldName) {
            $type = $metadata->getTypeOfField($fieldName);
            $filterType = $mapping[$type];

            if (!is_null($filterType)) {
                $fields[$fieldName] = $filterType;
            }
        }

        return $fields;
    }

}
