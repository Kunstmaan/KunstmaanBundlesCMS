<?php

namespace Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Symfony\Component\Yaml\Yaml;
use Kunstmaan\PagePartBundle\PageTemplate\Row;
use Kunstmaan\PagePartBundle\PageTemplate\Region;
use Symfony\Component\HttpKernel\KernelInterface;
use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfigurator;
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
        $types = array();
        foreach ($rawdata["types"] as $rawType) {
            $types[] = array("name"=>$rawType["name"], "class"=>$rawType["class"]);
        }
        $result->setPossiblePagePartTypes($types);
        $result->setDefaultContext($name);

        return $result;
    }
    /**
     * name: "Banners"
types:
    - { name: "Header", class: "Kunstmaan\PagePartBundle\Entity\HeaderPagePart" }
    - { name: "Text", class: "Kunstmaan\PagePartBundle\Entity\TextPagePart" }
    - { name: "Line", class: "Kunstmaan\PagePartBundle\Entity\LinePagePart" }
    - { name: "Image", class: "Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart" }
     */
}
