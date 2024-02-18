<?php

namespace Kunstmaan\AdminListBundle\Exception;

class ExportException extends \RuntimeException
{
    protected $data;

    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct($message, $data, $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
