<?php

namespace Kunstmaan\TranslatorBundle\Service\Command\Importer;

use Box\Spout\Common\Type;
use Box\Spout\Reader\ODS\Sheet;
use Box\Spout\Reader\ReaderFactory;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;
use Kunstmaan\TranslatorBundle\Service\TranslationGroupManager;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Translation\Loader\LoaderInterface;

class Importer
{
    /**
     * @var array
     */
    private $loaders = [];

    /**
     * @var TranslationGroupManager
     */
    private $translationGroupManager;

    public function import(\Symfony\Component\Finder\SplFileInfo $file, $force = false)
    {
        $this->validateLoaders($this->loaders);

        $filename = $file->getFilename();
        list($domain, $locale, $extension) = explode('.', $filename);

        if (!isset($this->loaders[$extension]) || !$this->loaders[$extension] instanceof \Symfony\Component\Translation\Loader\LoaderInterface) {
            throw new \Exception(sprintf('Requested loader for extension .%s isnt set', $extension));
        }

        $loader = $this->loaders[$extension];
        $messageCatalogue = $loader->load($file->getPathname(), $locale, $domain);
        $importedTranslations = 0;

        foreach ($messageCatalogue->all($domain) as $keyword => $text) {
            if ($this->importSingleTranslation($keyword, $text, $locale, $filename, $domain, $force)) {
                ++$importedTranslations;
            }
        }

        return $importedTranslations;
    }

    /**
     * @param string $file
     * @param array  $locales
     * @param bool   $force
     *
     * @return int
     *
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     */
    public function importFromSpreadsheet(string $file, array $locales, $force = false)
    {
        $filePath = realpath(dirname($file)) . DIRECTORY_SEPARATOR;
        $fileName = basename($file);
        $file = $filePath . $fileName;

        if (!file_exists($file)) {
            throw new LogicException(sprintf('Can not find file in %s', $file));
        }

        $format = (new File($file))->guessExtension();
        if (!\in_array($format, [Type::CSV, Type::ODS, Type::XLSX], true)) {
            $format = Type::CSV;
        }

        $headers = ['domain', 'keyword'];
        $requiredHeaders = array_merge($headers, $locales);
        $requiredHeaders = array_map('strtolower', $requiredHeaders);

        try {
            $reader = ReaderFactory::create($format);
            $reader->open($file);
        } catch (\Exception $e) {
            throw new LogicException('Format has to be either xlsx, ods or cvs');
        }
        $sheets = $reader->getSheetIterator();

        /** @var Sheet $sheet */
        $importedTranslations = 0;
        foreach ($sheets as $sheet) {
            $rows = $sheet->getRowIterator();
            $headers = [];

            /** @var array $row */
            foreach ($rows as $row) {
                if (empty($headers)) {
                    $headers = $row;
                    $headers = array_map('strtolower', $headers);
                    foreach ($requiredHeaders as $header) {
                        if (!\in_array($header, $headers)) {
                            throw new LogicException(sprintf('Header: %s, should be present in the file!', $header));
                        }
                    }

                    continue;
                }
                $domain = $row[array_search('domain', $headers)];
                $keyword = $row[array_search('keyword', $headers)];
                foreach ($locales as $locale) {
                    $this->importSingleTranslation($keyword, $row[array_search($locale, $headers)], $locale, $file, $domain, $force);
                    ++$importedTranslations;
                }
            }

            break;
        }
        $reader->close();

        return $importedTranslations;
    }

    /**
     * @param      $keyword
     * @param      $text
     * @param      $locale
     * @param      $filename
     * @param      $domain
     * @param bool $force
     *
     * @return bool
     */
    private function importSingleTranslation($keyword, $text, $locale, $filename, $domain, $force = false)
    {
        if (strlen($keyword) > 255) {
            return false;
        }

        $translationGroup = $this->translationGroupManager->getTranslationGroupByKeywordAndDomain($keyword, $domain);

        if (!($translationGroup instanceof TranslationGroup)) {
            $translationGroup = $this->translationGroupManager->create($keyword, $domain);
        }

        $translation = $this->translationGroupManager->addTranslation($translationGroup, $locale, $text, $filename);

        if (null === $translation && false === $force) {
            return false;
        }

        if (true === $force && null === $translation) {
            $this->translationGroupManager->updateTranslation($translationGroup, $locale, $text, $filename);

            return true;
        }

        return true;
    }

    /**
     * Validate the loaders
     *
     * @param array $loaders
     *
     * @throws \Exception If no loaders are defined
     */
    public function validateLoaders($loaders = [])
    {
        if (!is_array($loaders) || count($loaders) <= 0) {
            throw new \Exception('No translation file loaders tagged.');
        }
    }

    public function setLoaders(array $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * Adds a loader to the translation importer.
     *
     * @param string          $format The format of the loader
     * @param LoaderInterface $loader
     */
    public function addLoader($format, LoaderInterface $loader)
    {
        $this->loaders[$format] = $loader;
    }

    public function setTranslationGroupManager(TranslationGroupManager $translationGroupManager)
    {
        $this->translationGroupManager = $translationGroupManager;
    }
}
