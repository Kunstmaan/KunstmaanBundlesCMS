<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

class PageTemplateConfigurationReader implements PageTemplateConfigurationReaderInterface
{

    /**
     * @var PageTemplateConfigurationParserInterface
     */
    private $parser;

    /**
     * @param PageTemplateConfigurationParserInterface $parser
     */
    public function __construct(PageTemplateConfigurationParserInterface $parser)
    {
        $this->parser = $parser;
    }


    /**
     * @param HasPageTemplateInterface $page
     *
     * @throws \Exception
     * @return PageTemplateInterface[]
     */
    public function getPageTemplates(HasPageTemplateInterface $page)
    {
        $pageTemplates = [];
        foreach ($page->getPageTemplates() as $pageTemplate) {
            if (is_string($pageTemplate)) {
                $pt = $this->parser->parse($pageTemplate);
            } elseif ($pageTemplate instanceof PageTemplateInterface) {
                $pt = $pageTemplate;
            } else {
                throw new \Exception("don't know how to handle the pageTemplate " . get_class($pageTemplate));
            }

            $pageTemplates[$pt->getName()] = $pt;
        }

        return $pageTemplates;
    }
}
