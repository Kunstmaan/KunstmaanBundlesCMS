<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Importer;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Command\Importer\Importer;
use Kunstmaan\TranslatorBundle\Service\TranslationGroupManager;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Writer as CSVWriter;
use OpenSpout\Writer\ODS\Writer as ODSWriter;
use OpenSpout\Writer\WriterInterface;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Filesystem\Filesystem;

class ImporterSpreadsheetTest extends TestCase
{
    /** @var string */
    private $tmpDir;

    /** @var Translation[] */
    private $persisted = [];

    public function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/kuma-importer-spreadsheet-' . uniqid();
        (new Filesystem())->mkdir($this->tmpDir);
    }

    public function tearDown(): void
    {
        (new Filesystem())->remove($this->tmpDir);
    }

    public function getSpreadsheetWriters(): array
    {
        return [
            'csv' => ['csv', CSVWriter::class],
            'xlsx' => ['xlsx', XLSXWriter::class],
            'ods' => ['ods', ODSWriter::class],
        ];
    }

    /**
     * @dataProvider getSpreadsheetWriters
     */
    public function testImportFromSpreadsheet(string $extension, string $writerClass)
    {
        $file = $this->createSpreadsheet($extension, new $writerClass(), [
            ['domain', 'keyword', 'en', 'nl'],
            ['messages', 'headers.frontpage', 'FrontPage', 'Voorpagina'],
        ]);

        $importer = $this->createImporter();

        $this->assertSame(2, $importer->importFromSpreadsheet($file, ['en', 'nl'], true));
        $this->assertCount(2, $this->persisted);
        $this->assertSame('messages', $this->persisted[0]->getDomain());
        $this->assertSame('headers.frontpage', $this->persisted[0]->getKeyword());
        $this->assertSame('en', $this->persisted[0]->getLocale());
        $this->assertSame('FrontPage', $this->persisted[0]->getText());
        $this->assertSame('nl', $this->persisted[1]->getLocale());
        $this->assertSame('Voorpagina', $this->persisted[1]->getText());
    }

    public function testImportFromSpreadsheetWithUnsupportedFormat()
    {
        $file = $this->tmpDir . '/translations.json';
        file_put_contents($file, '{"messages":{"headers.frontpage":"FrontPage"}}');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Format has to be either xlsx, ods or cvs');

        $this->createImporter()->importFromSpreadsheet($file, ['en'], true);
    }

    public function testImportFromSpreadsheetWithMissingHeader()
    {
        $file = $this->createSpreadsheet('csv', new CSVWriter(), [
            ['domain', 'keyword', 'en'],
            ['messages', 'headers.frontpage', 'FrontPage'],
        ]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Header: nl, should be present in the file!');

        $this->createImporter()->importFromSpreadsheet($file, ['en', 'nl'], true);
    }

    private function createImporter(): Importer
    {
        $repository = $this->createMock(TranslationRepository::class);
        $repository->method('findAll')->willReturn([]);
        $repository->method('getUniqueTranslationId')->willReturn(1);
        $repository->method('persist')->willReturnCallback(function (Translation $translation) {
            $this->persisted[] = $translation;
        });

        return new Importer(new TranslationGroupManager($repository));
    }

    private function createSpreadsheet(string $extension, WriterInterface $writer, array $rows): string
    {
        $file = $this->tmpDir . '/translations.' . $extension;

        $writer->openToFile($file);
        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }
        $writer->close();

        return $file;
    }
}
