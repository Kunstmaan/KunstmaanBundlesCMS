<?php

namespace Kunstmaan\TranslatorBundle\Tests\Service\Exporter;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Kunstmaan\TranslatorBundle\Model\Export\ExportFile;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Command\Exporter\ExportCommandHandler;
use PHPUnit\Framework\TestCase;

class ExportCommandHandlerTest extends TestCase
{
    const TEST_DATA_DOMAIN = 'messages';
    const TEST_DATA_LOCALE = 'en';
    const TEST_DATA_EXT = 'yml';

    private $exportCommandHandler;

    public function setUp(): void
    {
        $translation = new Translation();
        $translation
            ->setDomain(self::TEST_DATA_DOMAIN)
            ->setLocale(self::TEST_DATA_LOCALE)
        ;

        $translationRepository = $this->createMock(TranslationRepository::class);
        $translationRepository->method('getTranslationsByLocalesAndDomains')->willReturn([$translation]);

        $this->exportCommandHandler = new ExportCommandHandler();
        $this->exportCommandHandler->setTranslationRepository($translationRepository);
    }

    public function testGetExportFiles()
    {
        $exportCommand = new ExportCommand();
        $exportCommand
                ->setDomains(self::TEST_DATA_DOMAIN)
                ->setLocales(self::TEST_DATA_LOCALE)
                ->setFormat(self::TEST_DATA_EXT);

        $files = $this->exportCommandHandler->getExportFiles($exportCommand);
        $this->assertInstanceOf(ArrayCollection::class, $files);
        $this->assertInstanceOf(ExportFile::class, $files->first());
    }
}
