<?php

namespace Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Symfony\Component\Yaml\Yaml;
use Kunstmaan\PagePartBundle\PageTemplate\Row;
use Kunstmaan\PagePartBundle\PageTemplate\Region;
use Symfony\Component\HttpKernel\KernelInterface;
/**
 * PageTemplateConfigurationReader
 */
class PageTemplateConfigurationReader
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
        $result = new PageTemplate();
        $path = $this->kernel->locateResource('@'.$namespace.'/Resources/config/pagetemplates/'.$name.'.yml');
        $rawdata = Yaml::parse($path);
        $result->setName($rawdata["name"]);
        $rows = array();
        foreach ($rawdata["rows"] as $rawRow) {
            $regions = array();
            foreach ($rawRow["regions"] as $rawRegion) {
                $regions[] = new Region($rawRegion["name"], $rawRegion["span"]);
            }
            $rows[] = new Row($regions);
        }
        $result->setRows($rows);
        $result->setTemplate($rawdata["template"]);

        return $result;
    }
}
