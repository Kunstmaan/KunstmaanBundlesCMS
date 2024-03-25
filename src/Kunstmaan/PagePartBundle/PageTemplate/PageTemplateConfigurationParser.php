<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class PageTemplateConfigurationParser implements PageTemplateConfigurationParserInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    private $presets = [];

    public function __construct(KernelInterface $kernel, array $presets = [])
    {
        $this->kernel = $kernel;
        $this->presets = $presets;
    }

    /**
     * This will read the $name file and parse it to the PageTemplate
     *
     * @param string $name
     *
     * @return PageTemplateInterface
     *
     * @throws \Exception
     */
    public function parse($name)
    {
        $rawData = $this->getRawData($name);

        $result = new PageTemplate();
        $result->setName($rawData['name']);
        $rows = [];
        foreach ($rawData['rows'] as $rawRow) {
            $regions = [];
            foreach ($rawRow['regions'] as $rawRegion) {
                $region = $this->buildRegion($rawRegion);
                $regions[] = $region;
            }
            $rows[] = new Row($regions);
        }

        $result->setRows($rows);
        $result->setTemplate($rawData['template']);

        return $result;
    }

    /**
     * This builds a Region out of the rawRegion from the Yaml
     *
     * @param array $rawRegion
     */
    private function buildRegion($rawRegion): Region
    {
        $children = [];
        $rows = [];
        $rawRegion = array_replace(['regions' => [], 'rows' => []], $rawRegion);

        foreach ($rawRegion['regions'] as $child) {
            $children[] = $this->buildRegion($child);
        }

        foreach ($rawRegion['rows'] as $row) {
            $rows[] = $this->buildRow($row);
        }

        $rawRegion = array_replace([
            'name' => null,
            'span' => 12,
            'template' => null,
        ], $rawRegion);

        return new Region($rawRegion['name'], $rawRegion['span'], $rawRegion['template'], $children, $rows);
    }

    /**
     * This builds a Row out of the rawRow from the Yaml
     *
     * @param array $rawRow
     */
    private function buildRow($rawRow): Row
    {
        $regions = [];

        foreach ($rawRow as $region) {
            $regions[] = $this->buildRegion($region);
        }

        return new Row($regions);
    }

    /**
     * @throws \Exception
     */
    private function getRawData($name): array
    {
        if (isset($this->presets[$name])) {
            return $this->presets[$name];
        }

        // if we use the old flow (sf3), the raw data can be stored in it's own yml file
        if (strpos($name, ':')) {
            $nameParts = explode(':', $name, 2);
            if (2 !== \count($nameParts)) {
                throw new \Exception(sprintf('Malformed namespaced configuration name "%s" (expecting "namespace:pagename").', $name));
            }
            list($namespace, $name) = $nameParts;
            $path = $this->kernel->locateResource('@' . $namespace . '/Resources/config/pagetemplates/' . $name . '.yml');

            return Yaml::parse(file_get_contents($path));
        }

        throw new \Exception(sprintf('Non existing template "%s".', $name));
    }
}
