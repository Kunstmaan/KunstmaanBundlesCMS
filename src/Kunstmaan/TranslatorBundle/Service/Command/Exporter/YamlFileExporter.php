<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Exporter;

use Symfony\Component\Yaml\Dumper;

/**
 * Export into yaml format
 */
class YamlFileExporter implements FileExporterInterface
{
    public function export(array $translations)
    {
        $ymlDumper = new Dumper();

        return $ymlDumper->dump($translations);
    }

    public function supports($format)
    {
        return 'yml' === $format;
    }
}
