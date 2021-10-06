<?php

namespace Kunstmaan\AdminListBundle\Service;

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Kunstmaan\AdminListBundle\AdminList\ExportableInterface;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\AdminListBundle\Exception\ExportException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ExportService
{
    const SUPPORTED_EXTENSIONS = [
        'Csv' => Type::CSV,
        'Ods' => Type::ODS,
        'Excel' => Type::XLSX,
    ];

    /**
     * @return array
     */
    public static function getSupportedExtensions()
    {
        return self::SUPPORTED_EXTENSIONS;
    }

    /** @var Environment */
    private $twig;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(Environment $twig, TranslatorInterface $translator)
    {
        $this->twig = $twig;
        $this->translator = $translator;
    }

    /**
     * @param string $format
     *
     * @return StreamedResponse
     *
     * @throws ExportException
     */
    public function getDownloadableResponse(ExportableInterface $adminList, $format)
    {
        return $this->streamOutput($adminList, $format);
    }

    /**
     * @param string $format
     *
     * @return StreamedResponse
     *
     * @throws ExportException
     */
    protected function streamOutput(ExportableInterface $adminList, $format)
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use ($adminList, $format) {
            $writer = WriterFactory::create($format);
            $writer->openToBrowser('export.' . $format);

            $row = [];
            /** @var Field $column */
            foreach ($adminList->getExportColumns() as $column) {
                $row[] = $this->translator->trans($column->getHeader(), [], null, 'en');
            }
            $writer->addRow($row);

            $iterator = $adminList->getIterator();
            $rows = [];
            foreach ($iterator as $item) {
                if (\array_key_exists(0, $item)) {
                    $itemObject = $item[0];
                } else {
                    $itemObject = $item;
                }

                $row = [];
                /** @var Field $column */
                foreach ($adminList->getExportColumns() as $column) {
                    $columnName = $column->getName();
                    $itemHelper = $itemObject;
                    if ($column->hasAlias()) {
                        $itemHelper = $column->getAliasObj($itemObject);
                        $columnName = $column->getColumnName($columnName);
                    }
                    $data = $adminList->getStringValue($itemHelper, $columnName);
                    if (null !== $column->getTemplate()) {
                        if (!$this->twig->getLoader()->exists($column->getTemplate())) {
                            throw new ExportException('No export template defined for ' . \get_class($data), $data);
                        }

                        $data = $this->twig->render($column->getTemplate(), ['object' => $data]);
                    }

                    $row[] = $data;
                }
                $rows[] = $row;
            }

            $writer->addRows($rows);
            $writer->close();
        });

        return $response;
    }
}
