<?php

namespace Kunstmaan\AdminBundle\Event;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Form\UserType;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdaptSimpleFormEvent
 * @package Kunstmaan\AdminBundle\Event
 */
class AdaptSimpleFormEvent extends Event
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var AbstractType
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
     * @param Request $request
     * @param AbstractType $formType
     * @param $data
     */
    public function __construct(Request $request , AbstractType $formType , $data)
    {
        $this->request = $request;
        $this->formType = $formType;
        $this->data = $data;
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
     * @return AbstractType
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @param AbstractType $type
     */
    public function setFormType(AbstractType $type)
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
}