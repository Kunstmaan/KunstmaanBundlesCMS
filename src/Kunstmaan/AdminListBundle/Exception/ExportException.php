<?php

namespace Kunstmaan\AdminListBundle\Exception;

class ExportException extends \RuntimeException
{
    /** @var mixed */
    protected $data;

    /**
     * @param string $message
     * @param mixed  $data
     * @param int    $code
     */
    public function __construct($message = '', $data, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
