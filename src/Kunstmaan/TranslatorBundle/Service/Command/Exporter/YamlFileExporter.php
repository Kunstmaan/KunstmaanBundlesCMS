<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Exporter;

use Symfony\Component\Yaml\Dumper;

/**
 * Export into yaml format
 */
class YamlFileExporter implements FileExporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function export(array $translations)
    {
        $ymlDumper = new Dumper();
        $ymlContent = $ymlDumper->dump($translations);

        return $ymlContent;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        return 'yml' === $format;
    }
}
