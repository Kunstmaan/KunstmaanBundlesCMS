<?php

namespace Kunstmaan\AdminListBundle\Service;

use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    private $renderer;

    const EXT_CSV = 'csv';
    const EXT_EXCEL = 'xlsx';

    public static function getSupportedExtensions()
    {
        $rfl = new \ReflectionClass(new self());
        $data = $rfl->getConstants();

        $extensions = array();
        foreach ($data as $name => $ext) {
            if (strpos($name, 'EXT_') !== false) {
                $key = ucfirst(strtolower(str_replace('EXT_', '', $name)));
                $extensions[$key] = $ext;
            }
        }

        return $extensions;
    }

    public function getDownloadableResponse(AdminList $adminlist, $_format, $template = null)
    {
        switch ($_format) {
            case self::EXT_EXCEL:
                $writer = $this->createExcelSheet($adminlist);
                $response = $this->createResponseForExcel($writer);
                break;
            default:
                $content = $this->createFromTemplate($adminlist, $_format, $template);
                $response = $this->createResponse($content, $_format);
                break;
        }

        $filename = sprintf('entries.%s', $_format);
        $response->headers->set('Content-Disposition', sprintf('attachment; filename=%s', $filename));
        return $response;
    }

    public function createFromTemplate(AdminList $adminlist, $_format, $template = null){
        if($template === null) {
            $template = sprintf("KunstmaanAdminListBundle:Default:export.%s.twig", $_format);
        }

        $allIterator = $adminlist->getAllIterator();
        return $this->renderer->render($template, array(
            "iterator" => $allIterator,
            "adminlist" => $adminlist,
            "queryparams" => array()
        ));
    }

    /**
     * @param $adminlist
     * @return \PHPExcel_Writer_Excel2007
     * @throws \Exception
     * @throws \PHPExcel_Exception
     */
    public function createExcelSheet(AdminList $adminlist)
    {
        $objPHPExcel = new \PHPExcel();

        $objWorksheet = $objPHPExcel->getActiveSheet();

        $number = 1;

        $row = array();
        foreach ($adminlist->getExportColumns() as $column) {
            $row[] = $column->getHeader();
        }
        $objWorksheet->fromArray($row, null, 'A' . $number++);

        $allIterator = $adminlist->getAllIterator();
        foreach($allIterator as $item) {
            if (array_key_exists(0, $item)) {
                $itemObject = $item[0];
            } else {
                $itemObject = $item;
            }

            $row = array();
            foreach ($adminlist->getExportColumns() as $column) {
                $data = $adminlist->getStringValue($itemObject, $column->getName());
                if (is_object($data)) {
                    if (!$this->renderer->exists($column->getTemplate())) {
                        throw new \Exception('No export template defined for ' . get_class($data));
                    }

                    $data = $this->renderer->render($column->getTemplate(), array("object" => $data));
                }

                $row[] = $data;
            }
            $objWorksheet->fromArray($row, null, 'A' . $number++);
        }

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

        return $objWriter;
    }

    public function createResponse($content, $_format)
    {
        $response = new Response();
        $response->headers->set('Content-Type', sprintf('text/%s', $_format));
        $response->setContent($content);

        return $response;
    }

    public function createResponseForExcel($writer)
    {
        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        return $response;
    }

    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }
}
