<?php

namespace Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Symfony\Component\Yaml\Yaml;
use Kunstmaan\PagePartBundle\PageTemplate\Row;
use Kunstmaan\PagePartBundle\PageTemplate\Region;
use Symfony\Component\HttpKernel\KernelInterface;
use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
/**
 * PagePartConfigurationReader
 */
class PagePartConfigurationReader
{

    private $kernel;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * This will read the $name file and parse it to the PageTemplate
     * @param string $name
     *
     * @return PageTemplate
     */
    public function parse($name)
    {
        if (false === $pos = strpos($name, ':')) {
            throw new \Exception(sprintf('Malformed namespaced configuration name "%s" (expecting "namespace:pagename.yml").', $name));
        }
        $namespace = substr($name, 0, $pos);
        $name = substr($name, $pos + 1);
        $result = new PagePartAdminConfigurator();
        $path = $this->kernel->locateResource('@'.$namespace.'/Resources/config/pageparts/'.$name.'.yml');
        $rawdata = Yaml::parse($path);
        $result->setName($rawdata["name"]);
        $result->setInternalName($name);
        $types = array();
        foreach ($rawdata["types"] as $rawType) {
            $types[] = array("name"=>$rawType["name"], "class"=>$rawType["class"]);
        }
        $result->setPossiblePagePartTypes($types);
        $result->setContext($rawdata["context"]);

        return $result;
    }

    /**
     * @param HasPagePartsInterface $page
     *
     * @throws \Exception
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurators(HasPagePartsInterface $page)
    {
        $pagePartAdminConfigurators = array();
        foreach ($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration) {
            if (is_string($pagePartAdminConfiguration)) {
                $pagePartAdminConfigurators[] = $this->parse($pagePartAdminConfiguration);
            } else if (is_object($pagePartAdminConfiguration) && $pagePartAdminConfiguration instanceof AbstractPagePartAdminConfigurator) {
                $pagePartAdminConfigurators[] = $pagePartAdminConfiguration;
            } else {
                throw new \Exception("don't know how to handle the pagePartAdminConfiguration " . get_class($pagePartAdminConfiguration));
            }
        }

        return $pagePartAdminConfigurators;
    }

    /**
     * @param HasPagePartsInterface $page
     *
     * @throws \Exception
     * @return string[]
     */
    public function getPagePartContexts(HasPagePartsInterface $page)
    {
        $result = array();

        $pagePartAdminConfigurators = $this->getPagePartAdminConfigurators($page);
        foreach ($pagePartAdminConfigurators as $pagePartAdminConfigurator) {
            $context = $pagePartAdminConfigurator->getContext();
            if (!in_array($context, $result)) {
                $result[] = $context;
            }
        }

        return $result;
    }
}
