<?php

namespace Kunstmaan\PagePartBundle\PagePartConfigurationReader;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;

class PagePartConfigurationReader implements PagePartConfigurationReaderInterface
{
    /**
     * @var PagePartAdminConfiguratorInterface[]
     */
    protected $configurators = [];

    /**
     * @var PagePartConfigurationParserInterface
     */
    protected $parser;

    public function __construct(PagePartConfigurationParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return PagePartAdminConfiguratorInterface[]
     *
     * @throws \Exception
     */
    public function getPagePartAdminConfigurators(HasPagePartsInterface $page)
    {
        $pagePartAdminConfigurators = [];
        foreach ($page->getPagePartAdminConfigurations() as $value) {
            if ($value instanceof PagePartAdminConfiguratorInterface) {
                $pagePartAdminConfigurators[] = $value;
            } elseif (\is_string($value) && isset($this->configurators[$value])) {
                $pagePartAdminConfigurators[] = $this->configurators[$value];
            } elseif (\is_string($value)) {
                $this->configurators[$value] = $this->parser->parse($value, $this->configurators);
                $pagePartAdminConfigurators[] = $this->configurators[$value];
            } else {
                throw new \Exception("don't know how to handle the pagePartAdminConfiguration " . \get_class($value));
            }
        }

        return $pagePartAdminConfigurators;
    }

    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function getPagePartContexts(HasPagePartsInterface $page)
    {
        $result = [];

        $pagePartAdminConfigurators = $this->getPagePartAdminConfigurators($page);
        foreach ($pagePartAdminConfigurators as $pagePartAdminConfigurator) {
            $context = $pagePartAdminConfigurator->getContext();
            if (!\in_array($context, $result)) {
                $result[] = $context;
            }
        }

        return $result;
    }
}
