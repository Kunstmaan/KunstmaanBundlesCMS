<?php

namespace Kunstmaan\AdminBundle\Event;

use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class AdaptSimpleFormEvent extends Event
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
     * @var TabPane|null
     */
    protected $tabPane;

    protected $data;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string $formType
     */
    public function __construct(Request $request, $formType, $data, $options = [])
    {
        $this->request = $request;
        $this->formType = $formType;
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * @return TabPane|null
     */
    public function getTabPane()
    {
        return $this->tabPane;
    }

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

    public function getData()
    {
        return $this->data;
    }

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

    public function setOptions($options)
    {
        $this->options = $options;
    }
}
