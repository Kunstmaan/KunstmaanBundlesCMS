<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates all classes for an admin list
 */
class AdminListGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
{
    /**
     * @var string
     */
    private $skeletonDir;

    private $questionHelper;

    /**
     * @param string $skeletonDir The directory of the skeleton
     */
    public function __construct($skeletonDir)
    {
        $this->skeletonDir = $skeletonDir;
    }

    public function setQuestion($questionHelper)
    {
        $this->questionHelper = $questionHelper;
    }

    /**
     * @param Bundle          $bundle   The bundle
     * @param string          $entity   The entity name
     * @param ClassMetadata   $metadata The meta data
     * @param OutputInterface $output
     *
     * @internal param bool $generateAdminType True if we need to specify the admin type
     *
     * @return void
     */
    public function generate(Bundle $bundle, $entity, ClassMetadata $metadata, OutputInterface $output)
    {
        $parts             = explode('\\', $entity);
        $entityName        = array_pop($parts);
        $generateAdminType = !method_exists($entity, 'getAdminType');

        if ($generateAdminType) {
            try {
                $this->generateAdminType($bundle, $entityName, $metadata);
                $output->writeln('Generating the Type code: <info>OK</info>');
            } catch (\Exception $error) {
                $output->writeln(
                    $this->questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error')
                );
                $output->writeln('Generating the Type code: <error>ERROR</error>');
            }
        }

        try {
            $this->generateConfiguration($bundle, $entityName, $metadata, $generateAdminType);
            $output->writeln('Generating the Configuration code: <info>OK</info>');
        } catch (\Exception $error) {
            $output->writeln(
                $this->questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error')
            );
            $output->writeln('Generating the Configuration code: <error>ERROR</error>');
        }


        try {
            $this->generateController($bundle, $entityName, $metadata);
            $output->writeln('Generating the Controller code: <info>OK</info>');
        } catch (\Exception $error) {
            $output->writeln(
                $this->questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error')
            );
            $output->writeln('Generating the Controller code: <error>ERROR</error>');
        }
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
        $dirPath   = sprintf("%s/AdminList", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));

        if (file_exists($classPath)) {
            throw new \RuntimeException(
                sprintf(
                    'Unable to generate the %s class as it already exists under the %s file',
                    $className,
                    $classPath
                )
            );
        }
        $this->setSkeletonDirs(array($this->skeletonDir));
        $this->renderFile(
            '/AdminList/AdminListConfigurator.php',
            $classPath,
            array(
                'namespace'           => $bundle->getNamespace(),
                'bundle'              => $bundle,
                'entity_class'        => $entityName,
                'fields'              => $this->getFieldsWithFilterTypeFromMetadata($metadata),
                'generate_admin_type' => $generateAdminType
            )
        );
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param string $entityName The entity name
     *
     * @throws \RuntimeException
     */
    public function generateController(Bundle $bundle, $entityName)
    {
        $className  = sprintf("%sAdminListController", $entityName);
        $dirPath    = sprintf("%s/Controller", $bundle->getPath());
        $classPath  = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));
        $extensions = 'csv';
        if (class_exists("\\Kunstmaan\\AdminListBundle\\Service\\ExportService")) {
            $extensions = implode('|', \Kunstmaan\AdminListBundle\Service\ExportService::getSupportedExtensions());
        }

        if (file_exists($classPath)) {
            throw new \RuntimeException(
                sprintf(
                    'Unable to generate the %s class as it already exists under the %s file',
                    $className,
                    $classPath
                )
            );
        }

        $this->setSkeletonDirs(array($this->skeletonDir));
        $this->renderFile(
            '/Controller/EntityAdminListController.php',
            $classPath,
            array(
                'namespace'         => $bundle->getNamespace(),
                'bundle'            => $bundle,
                'entity_class'      => $entityName,
                'export_extensions' => $extensions
            )
        );

    }

    /**
     * @param Bundle        $bundle     The bundle
     * @param string        $entityName The entity name
     * @param ClassMetadata $metadata   The meta data
     *
     * @throws \RuntimeException
     */
    public function generateAdminType(Bundle $bundle, $entityName, ClassMetadata $metadata)
    {
        $className = sprintf("%sAdminType", $entityName);
        $dirPath   = sprintf("%s/Form", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));

        if (file_exists($classPath)) {
            throw new \RuntimeException(
                sprintf(
                    'Unable to generate the %s class as it already exists under the %s file',
                    $className,
                    $classPath
                )
            );
        }

        $this->setSkeletonDirs(array($this->skeletonDir));
        $this->renderFile(
            '/Form/EntityAdminType.php',
            $classPath,
            array(
                'namespace'    => $bundle->getNamespace(),
                'bundle'       => $bundle,
                'entity_class' => $entityName,
                'fields'       => $this->getFieldsFromMetadata($metadata)
            )
        );
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
            'string'   => 'ORM\StringFilterType',
            'text'     => 'ORM\StringFilterType',
            'integer'  => 'ORM\NumberFilterType',
            'smallint' => 'ORM\NumberFilterType',
            'bigint'   => 'ORM\NumberFilterType',
            'decimal'  => 'ORM\NumberFilterType',
            'boolean'  => 'ORM\BooleanFilterType',
            'date'     => 'ORM\DateFilterType',
            'datetime' => 'ORM\DateFilterType',
            'time'     => 'ORM\DateFilterType'
        );

        $fields = array();

        foreach ($this->getFieldsFromMetadata($metadata) as $fieldName) {
            $type       = $metadata->getTypeOfField($fieldName);
            $filterType = isset($mapping[$type]) ? $mapping[$type] : null;

            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $fieldName, $matches);
            $fieldTitle = ucfirst(strtolower(implode(' ', $matches[0])));

            if (!is_null($filterType)) {
                $fields[$fieldName] = array('filterType' => $filterType, 'fieldTitle' => $fieldTitle);
            }
        }

        return $fields;
    }

}
