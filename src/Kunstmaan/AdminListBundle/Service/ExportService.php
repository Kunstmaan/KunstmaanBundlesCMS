<?php

namespace Kunstmaan\AdminListBundle\Service;

use Box\Spout\Writer\WriterInterface;
use Kunstmaan\AdminListBundle\AdminList\ExportableInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Translation\Translator;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class ExportService
{
    /**
     * @var EngineInterface
     */
    private $renderer;

    /**
     * @var Translator
     */
    private $translator;

    const EXT_CSV   = 'csv';
    const EXT_EXCEL = 'xlsx';

    /**
     * @return array
     */
    public static function getSupportedExtensions()
    {
        $rfl  = new \ReflectionClass(new self());
        $data = $rfl->getConstants();

        $extensions = array();
        foreach ($data as $name => $ext) {
            if (strpos($name, 'EXT_') !== false) {
                $key              = ucfirst(strtolower(str_replace('EXT_', '', $name)));
                $extensions[$key] = $ext;
            }
        }

        return $extensions;
    }

    /**
     * @param ExportableInterface $adminList
     * @param string              $_format
     * @param string|null         $template
     *
     * @return Response|StreamedResponse
     *
     * @throws \Exception
     */
    public function getDownloadableResponse(ExportableInterface $adminList, $_format, $template = null)
    {
        switch ($_format) {
            case self::EXT_EXCEL:
                $response = $this->streamExcelSheet($adminList);
                break;
            default:
                $content  = $this->createFromTemplate($adminList, $_format, $template);
                $response = $this->createResponse($content, $_format);
                $filename = sprintf('entries.%s', $_format);
                $response->headers->set('Content-Disposition', sprintf('attachment; filename=%s', $filename));
                break;
        }

        return $response;
    }

    /**
     * @param ExportableInterface $adminList
     * @param string              $_format
     * @param string|null         $template
     *
     * @return string
     */
    public function createFromTemplate(ExportableInterface $adminList, $_format, $template = null)
    {
        if ($template === null) {
            $template = sprintf("KunstmaanAdminListBundle:Default:export.%s.twig", $_format);
        }

        $iterator = $adminList->getIterator();

        return $this->renderer->render(
            $template,
            array(
                'iterator'    => $iterator,
                'adminlist'   => $adminList,
                'queryparams' => array()
            )
        );
    }

    /**
     * @param ExportableInterface $adminList
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function streamExcelSheet(ExportableInterface $adminList)
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use ($adminList) {
            $writer = WriterFactory::create(Type::XLSX);
            $writer->openToBrowser("export.xlsx");

            $row = array();
            foreach ($adminList->getExportColumns() as $column) {
                $row[] = $this->translator->trans($column->getHeader());
            }
            $writer->addRow($row);

            $iterator = $adminList->getIterator();
            $rows = array();
            foreach ($iterator as $item) {
                if (array_key_exists(0, $item)) {
                    $itemObject = $item[0];
                } else {
                    $itemObject = $item;
                }

                $row = array();
                foreach ($adminList->getExportColumns() as $column) {
                    $data = $adminList->getStringValue($itemObject, $column->getName());
                    if (is_object($data)) {
                        if (!$this->renderer->exists($column->getTemplate())) {
                            throw new \Exception('No export template defined for ' . get_class($data));
                        }

                        $data = $this->renderer->render($column->getTemplate(), array('object' => $data));
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
     * @param string $content
     * @param string $_format
     *
     * @return Response
     */
    public function createResponse($content, $_format)
    {
        $response = new Response();
        $response->headers->set('Content-Type', sprintf('text/%s', $_format));
        $response->setContent($content);

        return $response;
    }

    /**
     * @param EngineInterface $renderer
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
}
