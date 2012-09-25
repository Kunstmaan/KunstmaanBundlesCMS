<?php
namespace Kunstmaan\GeneratorBundle\Helper;
use Doctrine\ORM\Mapping\ClassMetadata;

class GeneratorUtils
{
    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param ClassMetadataInfo $metadata
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
     * @param $string Text to be added in front of the file
     * @param $filename File to prepend in
     */
    public static function prepend($string, $filename) {
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
     * @param $toreplace Text to be replaced
     * @param $replace_text Text as replacement
     * @param $filename File to replace in
     */
    public static function replace($to_replace, $replace_text, $filename) {
        $content = @file_get_contents($filename);
        if($content) {
            $content = str_replace($to_replace, $replace_text, $content);
            file_put_contents($filename, $content);
        }

    }
}
