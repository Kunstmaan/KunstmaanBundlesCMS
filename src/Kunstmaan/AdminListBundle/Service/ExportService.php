<?php

namespace Kunstmaan\AdminListBundle\Service;

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

    public function getDownloadableResponse($adminlist, $_format, $template = null)
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

    public function createFromTemplate($adminlist, $_format, $template = null){
        if($template == null) {
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
     */
    public function createExcelSheet($adminlist)
    {
        $objPHPExcel = new \PHPExcel();

        $objWorksheet = $objPHPExcel->getActiveSheet();

        $number = 1;

        $row = array();
        foreach ($adminlist->getExportColumns() as $column) {
            $row[] = $this->convertToString($column->getHeader());
        }
        $objWorksheet->fromArray($row,NULL,'A'.$number++);

        $allIterator = $adminlist->getAllIterator();
        foreach($allIterator as $item){
            $row = array();
            foreach ($adminlist->getExportColumns() as $column) {
                $method = 'get'.$column->getName();
                $row[] = $this->convertToString($item[0]->$method());
            }
            $objWorksheet->fromArray($row,NULL,'A'.$number++);
        }

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        return $objWriter;
    }

    private function convertToString($var)
    {
        if ($var instanceof \DateTime) {
            return $var->format('Y-m-d H:i:s');
        }

        return $var;
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