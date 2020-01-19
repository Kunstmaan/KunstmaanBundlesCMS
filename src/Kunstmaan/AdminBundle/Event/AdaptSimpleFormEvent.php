<?php

namespace Kunstmaan\AdminBundle\Event;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdaptSimpleFormEvent
 */
class AdaptSimpleFormEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $formType;

    /**
     * @var TabPane
     */
    protected $tabPane;

    /**
     * @var
     */
    protected $data;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param Request $request
     * @param string  $formType
     * @param $data
     */
    public function __construct(Request $request, $formType, $data, $options = array())
    {
        $this->request = $request;
        $this->formType = $formType;
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * @return TabPane
     */
    public function getTabPane()
    {
        return $this->tabPane;
    }

    /**
     * @param TabPane $tabPane
     */
    public function setTabPane(TabPane $tabPane)
    {
        $this->tabPane = $tabPane;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @param string $type
     */
    public function setFormType($type)
    {
        $this->formType = $type;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}
