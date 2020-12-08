<?php

namespace Kunstmaan\AdminListBundle\Service;

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Kunstmaan\AdminListBundle\AdminList\ExportableInterface;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\AdminListBundle\Exception\ExportException;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface as LegaceTranslatorInterface;
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

    /**
     * @var EngineInterface|Environment
     */
    private $renderer;

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Environment $twig = null, $translator = null)
    {
        if (null === $twig) {
            @trigger_error(sprintf('Not passing the Twig service as the first argument of "%s" is deprecated since KunstmaanAdminListBundle 5.4 and will be required in KunstmaanAdminListBundle 6.0. Injected the required services in the constructor instead.', __METHOD__), E_USER_DEPRECATED);
        }

        if (null === $translator) {
            @trigger_error(sprintf('Not passing the "translator" service as the second argument of "%s" is deprecated since KunstmaanAdminListBundle 5.4 and will be required in KunstmaanAdminListBundle 6.0. Injected the required services in the constructor instead.', __METHOD__), E_USER_DEPRECATED);
        }

        if (null !== $translator && (!$translator instanceof LegaceTranslatorInterface && !$translator instanceof TranslatorInterface)) {
            throw new \InvalidArgumentException(sprintf('Argument 2 passed to "%s" must be of the type "%s" or "%s", "%s" given', __METHOD__, LegaceTranslatorInterface::class, TranslatorInterface::class, get_class($translator)));
        }

        $this->renderer = $twig;
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
                        // NEXT_MAJOR: Remove `templateExists` private method and call Twig exists check directly.
                        if (!$this->templateExists($column->getTemplate())) {
                            throw new ExportException('No export template defined for ' . \get_class($data), $data);
                        }

                        $data = $this->renderer->render($column->getTemplate(), ['object' => $data]);
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

    /**
     * @deprecated Setter injection is deprecation since KunstmaanAdminListBundle 5.4 and will be removed in KunstmaanAdminListBundle 6.0. Use constructor injection instead.
     *
     * @param EngineInterface $renderer
     */
    public function setRenderer($renderer)
    {
        if (!$this->renderer instanceof Environment) {
            if ($renderer instanceof EngineInterface) {
                @trigger_error(
                    sprintf('Injecting the template renderer with "%s" is deprecated since KunstmaanAdminListBundle 5.4 and will be removed in KunstmaanAdminListBundle 6.0. Inject Twig with constructor injection instead.', __METHOD__),
                    E_USER_DEPRECATED
                );
            }

            // Renderer was not set in the constructor, so set it here to the deprecated templating renderer. Constructor
            // value has precedence over the setter because the implementation is switched to twig.
            $this->renderer = $renderer;
        }
    }

    /**
     * @deprecated Setter injection is deprecation since KunstmaanAdminListBundle 5.4 and will be removed in KunstmaanAdminListBundle 6.0. Use constructor injection instead.
     *
     * @param Translator $translator
     */
    public function setTranslator($translator)
    {
        if (!$this->translator instanceof LegaceTranslatorInterface && !$this->translator instanceof TranslatorInterface) {
            if ($translator instanceof LegaceTranslatorInterface || $translator instanceof TranslatorInterface) {
                //Trigger deprecation because setter is deprecated, translator should be injected in the constructor
                @trigger_error(
                    sprintf('Injecting the translator with "%s" is deprecated since KunstmaanAdminListBundle 5.4 and will be removed in KunstmaanAdminListBundle 6.0. Inject the Translator with constructor injection instead.', __METHOD__),
                    E_USER_DEPRECATED
                );
            }

            $this->translator = $translator;
        }
    }

    private function templateExists(string $template)
    {
        if ($this->renderer instanceof EngineInterface) {
            return $this->renderer->exists($template);
        }

        return $this->renderer->getLoader()->exists($template);
    }
}
