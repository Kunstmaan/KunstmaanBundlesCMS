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
     *
     * @param string $name
     * @return PageTemplate
     * @throws \Exception
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
        $rawData = Yaml::parse($path);
        $result->setName($rawData['name']);
        $rows = array();
        foreach ($rawData['rows'] as $rawRow) {
            $regions = array();
            foreach ($rawRow['regions'] as $rawRegion) {
                $region = $this->buildRegion($rawRegion);
                $regions[] = $region;
            }
            $rows[] = new Row($regions);
        }
        $result->setRows($rows);
        $result->setTemplate($rawData["template"]);

        return $result;
    }

    /**
     * This builds a Region out of the rawRegion from the Yaml
     *
     * @param array $rawRegion
     * @return Region
     */
    private function buildRegion($rawRegion)
    {
        $children = array();

        if (isset($rawRegion['regions']) && count($rawRegion['regions'])) {
            foreach ($rawRegion['regions'] as $child) {
                $children[] = $this->buildRegion($child);
            }
        }

        return new Region(array_key_exists('name', $rawRegion) ? $rawRegion['name'] : null, $rawRegion['span'], array_key_exists('template', $rawRegion) ? $rawRegion['template'] : null, $children);
    }

    /**
     * @param HasPageTemplateInterface $page
     *
     * @throws \Exception
     * @return array(string => PageTemplate)
     */
    public function getPageTemplates(HasPageTemplateInterface $page)
    {
        $pageTemplates = array();
        foreach ($page->getPageTemplates() as $pageTemplate) {
            $pt = null;
            if (is_string($pageTemplate)) {
                $pt = $this->parse($pageTemplate);
            } else if (is_object($pageTemplate) && $pageTemplate instanceof PageTemplate) {
                $pt = $pageTemplate;
            } else {
                throw new \Exception("don't know how to handle the pageTemplate " . get_class($pageTemplate));
            }
            $pageTemplates[$pt->getName()] = $pt;
        }

        return $pageTemplates;
    }
}
