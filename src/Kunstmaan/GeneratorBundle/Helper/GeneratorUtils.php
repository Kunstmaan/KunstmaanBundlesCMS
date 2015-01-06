<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Doctrine\ORM\Mapping\ClassMetadata;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * GeneratorUtils
 */
class GeneratorUtils
{

    /**
     * Cleans the prefix. Prevents a double underscore from happening.
     *
     * @param $prefixString
     * @return string
     */
    public static function cleanPrefix($prefixString)
    {
        $prefixString = trim($prefixString);
        if (empty($prefixString)) {
            return null;
        }

        $result = preg_replace('/_*$/i', '', strtolower($prefixString)) . '_';

        if ($result == '_') {
            return null;
        }

        return $result;
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param ClassMetadata $metadata
     *
     * @return array $fields
     */
    public static function getFieldsFromMetadata(ClassMetadata $metadata)
    {
        $fields = (array) $metadata->fieldNames;

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            $fields = array_diff($fields, $metadata->identifier);
        }

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] !== ClassMetadata::ONE_TO_MANY) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }

    /**
     * Prepend the string in the file
     *
     * @param string $string   Text to be added in front of the file
     * @param string $filename File to prepend in
     */
    public static function prepend($string, $filename)
    {
        $context = stream_context_create();
        $fp = fopen($filename, 'r', 1, $context);
        $tmpname = md5($string);
        file_put_contents($tmpname, $string);
        file_put_contents($tmpname, $fp, FILE_APPEND);
        fclose($fp);
        unlink($filename);
        rename($tmpname, $filename);
    }

    /**
     * Append the string in the file
     *
     * @param string $string   Text to be added in front of the file
     * @param string $filename File to prepend in
     */
    public static function append($string, $filename)
    {
        $context = stream_context_create();
        $fp = fopen($filename, 'r', 1, $context);
        $tmpname = md5($string);
        file_put_contents($tmpname, $fp);
        file_put_contents($tmpname, $string, FILE_APPEND);
        fclose($fp);
        unlink($filename);
        rename($tmpname, $filename);
    }

    /**
     * Find and replace the string in the file
     *
     * @param string $toReplace   Text to be replaced
     * @param string $replaceText Text as replacement
     * @param string $filename    File to replace in
     */
    public static function replace($toReplace, $replaceText, $filename)
    {
        $content = file_get_contents($filename);
        if ($content) {
            $content = str_replace($toReplace, $replaceText, $content);
            file_put_contents($filename, $content);
        }

    }


    public static function getFullSkeletonPath($pathInSkeleton)
    {
        $pathInSkeleton = trim($pathInSkeleton);

        // pathInSkeleton needs to be prepended by a /
        if (substr($pathInSkeleton, 0, 1) !== '/') {
            $pathInSkeleton = '/' . $pathInSkeleton;
        }

        // Can't have a / at the end.
        if (substr($pathInSkeleton, -1) == '/') {
            $pathInSkeleton = rtrim($pathInSkeleton, '/');
        }

        return __DIR__ . '/../Resources/SensioGeneratorBundle/skeleton' . $pathInSkeleton;
    }

    public static function ensureOptionsProvided(InputInterface $input, array $options)
    {
        foreach ($options as $option) {
            if (null === $input->getOption($option)) {
                throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }
    }

    /**
     * Replaces '\' with '/'.
     *
     * @param $namespace
     * @return string
     */
    public static function fixNamespace($namespace)
    {
        return str_replace('\\', '/', $namespace);
    }

    /**
     * Returns an inputAssistant.
     *
     * This probably isn't the cleanest way. It'd be nicer if we could make a KunstmaanGenerator class
     * which all generators inherit from. It then provides a bunch of helper functions and a uniform manner
     * in which the input options are handled.
     *
     * @param InputInterface     $input
     * @param OutputInterface    $output
     * @param DialogHelper       $dialog
     * @param Kernel             $kernel
     * @param ContainerInterface $container
     *
     * @return InputAssistant
     */
    public static function getInputAssistant(InputInterface &$input, OutputInterface $output, DialogHelper $dialog, Kernel $kernel, ContainerInterface $container)
    {
        return new InputAssistant($input, $output, $dialog, $kernel, $container);
    }
}
