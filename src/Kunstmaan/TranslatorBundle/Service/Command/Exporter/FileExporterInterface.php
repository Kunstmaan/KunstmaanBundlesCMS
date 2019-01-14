<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Exporter;

interface FileExporterInterface
{
    /**
     * Export an array with translations into a string with the content of this type of file
     *
     * @param  array
     *
     * @return string content for the file
     */
    public function export(array $translations);

    /**
     * Check if the given format (extension) is supported
     *
     * @param string $format
     *
     * @return bool
     */
    public function supports($format);
}
