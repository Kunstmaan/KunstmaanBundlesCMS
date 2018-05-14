<?php
namespace Kunstmaan\PagePartBundle\PageTemplate;

/**
 * PageTemplate
 *
 * new PageTemplate("Content page", array(
                new Row(array(new Region("top",12))),
                new Row(array(new Region("left",2), new Region("main",10))),
                new Row(array(new Region("bottom",12)))
                ));
 */
class PageTemplate implements PageTemplateInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $templatePath;

    /**
     * @var Row[]
     */
    protected $rows;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return PageTemplate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param row[] $rows
     *
     * @return PageTemplate
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->templatePath;
    }

    /**
     * @param string $templatePath
     *
     * @return PageTemplate
     */
    public function setTemplate($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }
}
