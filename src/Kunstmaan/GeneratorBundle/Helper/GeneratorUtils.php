<?php

namespace Kunstmaan\GeneratorBundle\Helper;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * GeneratorUtils
 */
class GeneratorUtils
{

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
     * Find and replace the string in the file
     *
     * @param string $toReplace   Text to be replaced
     * @param string $replaceText Text as replacement
     * @param string $filename    File to replace in
     */
    public static function replace($toReplace, $replaceText, $filename)
    {
        $content = @file_get_contents($filename);
        if ($content) {
            $content = str_replace($toReplace, $replaceText, $content);
            file_put_contents($filename, $content);
        }

    }
}
