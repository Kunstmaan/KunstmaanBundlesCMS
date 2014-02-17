<?php

namespace Kunstmaan\AdminListBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    private $renderer;

    const EXT_CSV = 'csv';
    const EXT_EXCEL = 'xlsx';

    public function getSupportedExtensions()
    {
        $rfl = new \ReflectionClass($this);
        $data = $rfl->getConstants();

        $extensions = array();
        foreach ($data as $name => $ext) {
            $key = ucfirst(strtolower(str_replace('EXT_', '', $name)));
            $extensions[$key] = $ext;
        }

        return $extensions;
    }

    public function getDownloadableResponse($adminlist, $_format, $template = null){
        switch ($_format) {
            case self::EXT_EXCEL:
                $response = $this->createExcelSheet($adminlist);
                break;
            default:
                $response = $this->createFromTemplate($adminlist, $_format, $template);
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
        $response = new Response();
        $response->headers->set('Content-Type', sprintf('text/%s', $_format));
        $response->setContent($this->renderer->render($template, array(
            "iterator" => $allIterator,
            "adminlist" => $adminlist,
            "queryparams" => array()
        )));

        return $response;
    }

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
        $response = new StreamedResponse(
            function () use ($objWriter) {
                $objWriter->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        return $response;
    }

    private function convertToString($var)
    {
        if ($var instanceof \DateTime) {
            return $var->format('Y-m-d H:i:s');
        }

        return $var;
    }

    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }
}