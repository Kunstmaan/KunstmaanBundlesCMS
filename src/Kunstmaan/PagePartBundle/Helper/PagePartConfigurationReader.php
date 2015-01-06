<?php

namespace Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\KernelInterface;
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
     *
     * @param string $name
     * @return PageTemplate
     * @throws \Exception
     */
    public function parse($name)
    {
        $nameParts = explode(':', $name);
        if (count($nameParts) != 2)  {
            throw new \Exception(sprintf('Malformed namespaced configuration name "%s" (expecting "namespace:pagename.yml").', $name));
        }

        list($namespace, $name) = $nameParts;
        $path = $this->kernel->locateResource('@'.$namespace.'/Resources/config/pageparts/'.$name.'.yml');
        $rawData = Yaml::parse($path);
        if (!array_key_exists('types', $rawData)) {
            $rawData['types'] = array();
        }
        if (array_key_exists('extends', $rawData)) {
            if (!is_array($rawData['extends'])) {
                $rawData['extends'] = array($rawData['extends']);
            }
            foreach ($rawData['extends'] as $extend) {
                $recursiveResult = $this->parse($namespace.':'.$extend);
                $rawData['types'] = array_merge($recursiveResult->getPossiblePagePartTypes(), $rawData['types']);
            }
        }

        $types = array();
        foreach ($rawData['types'] as $type) {
            if ($type['class'] == '' || is_null($type['class'])) {
                if (array_key_exists($type['name'], $types)) {
                    unset($types[$type['name']]);
                }
            } else {
                $types[$type['name']] = array('name' => $type['name'], 'class' => $type['class']);
            }
        }

        $result = new PagePartAdminConfigurator();
        $result->setName($rawData['name']);
        $result->setInternalName($name);
        $result->setPossiblePagePartTypes(array_values($types));
        $result->setContext($rawData['context']);

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
