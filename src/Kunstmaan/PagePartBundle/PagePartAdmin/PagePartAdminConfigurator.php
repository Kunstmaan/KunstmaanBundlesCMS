<?php
namespace Kunstmaan\PagePartBundle\PagePartAdmin;

/**
 * PagePagePartAdminConfigurator
 */
class PagePartAdminConfigurator extends AbstractPagePartAdminConfigurator
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $internalName;


    /**
     * @var string
     */
    protected $context;

    /**
     * @var array
     */
    protected $pagePartTypes;

    /**
     * @var string
     */
    protected $widgetTemplate;

    /**
     * @return array
     */
    public function getPossiblePagePartTypes()
    {
        return $this->pagePartTypes;
    }

    /**
     * @param array $pagePartTypes
     */
    public function setPossiblePagePartTypes(array $pagePartTypes)
    {
        $this->pagePartTypes = $pagePartTypes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @param string $internalName
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getWidgetTemplate()
    {
        return $this->widgetTemplate;
    }

    /**
     * @param string $widgetTemplate
     */
    public function setWidgetTemplate($widgetTemplate)
    {
        $this->widgetTemplate = $widgetTemplate;
    }
}
