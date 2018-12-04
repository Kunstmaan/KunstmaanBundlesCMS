<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Exporter;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Symfony\Component\HttpFoundation\Response;

/**
 * Export into csv format
 */
class CSVFileExporter implements FileExporterInterface
{
    /** @var array */
    private $locales;

    /**
     * {@inheritdoc}
     */
    public function export(array $translations)
    {
        $handle = fopen('php://temp', 'r+');

        // Add the header of the CSV file
        $headers = ['Keyword', 'Domain'];

        // Get all possible locales and add them as header.
        $headers = array_merge($headers, $this->locales);

        fputcsv($handle, $headers);

        // Add all translation to the CSV.
        foreach ($translations as $key => $translation) {
            $values = \array_values($translation);
            $firstTranslation = \array_shift($values);
            $row = [$key, $firstTranslation->getDomain()];

            /* @var Translation $item */
            foreach ($this->locales as $locale) {
                $locale = preg_replace_callback('/\_([a-z]+)/', function ($match) {
                    return '_' . strtoupper($match[1]);
                }, $locale);

                if (isset($translation[$locale])) {
                    $item = $translation[$locale];
                    $row[] = utf8_decode($item->getText());
                } else {
                    $row[] = '';
                }
            }

            fputcsv($handle, $row);
        }

        rewind($handle);

        $response = new Response(stream_get_contents($handle));
        fclose($handle);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export_translations.csv"');

        return $response;
    }

    public function setLocales($locales)
    {
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        return 'csv' === $format;
    }
}
