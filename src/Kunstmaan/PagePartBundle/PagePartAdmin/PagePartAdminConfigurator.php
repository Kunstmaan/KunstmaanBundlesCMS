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
    protected $context;

    /**
     * @var array
     */
    protected $pagePartTypes;

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
    public function getDefaultContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     */
    public function setDefaultContext($context)
    {
        $this->context = $context;
    }
}
