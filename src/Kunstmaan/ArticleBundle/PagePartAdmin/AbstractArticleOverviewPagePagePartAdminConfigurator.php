<?php

namespace Kunstmaan\ArticleBundle\PagePartAdmin;

use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

/**
 * The PagePartAdminConfigurator for the AbstractArticleOverviewPage
 */
class AbstractArticleOverviewPagePagePartAdminConfigurator extends AbstractPagePartAdminConfigurator
{
    /**
     * @var array
     */
    protected $pagePartTypes;

    public function __construct(array $pagePartTypes = [])
    {
        $this->pagePartTypes = array_merge(
            [
                [
                    'name' => 'Header',
                    'class' => 'Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
                ],
                [
                    'name' => 'Text',
                    'class' => 'Kunstmaan\PagePartBundle\Entity\TextPagePart',
                ],
                [
                    'name' => 'Line',
                    'class' => 'Kunstmaan\PagePartBundle\Entity\LinePagePart',
                ],
                [
                    'name' => 'TOC',
                    'class' => 'Kunstmaan\PagePartBundle\Entity\TocPagePart',
                ],
                [
                    'name' => 'Link',
                    'class' => 'Kunstmaan\PagePartBundle\Entity\LinkPagePart',
                ],
                [
                    'name' => 'To Top',
                    'class' => 'Kunstmaan\PagePartBundle\Entity\ToTopPagePart',
                ],
                [
                    'name' => 'Image',
                    'class' => 'Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart',
                ],
                [
                    'name' => 'Download',
                    'class' => 'Kunstmaan\MediaPagePartBundle\Entity\DownloadPagePart',
                ],
                [
                    'name' => 'Slide',
                    'class' => 'Kunstmaan\MediaPagePartBundle\Entity\SlidePagePart',
                ],
                [
                    'name' => 'Video',
                    'class' => 'Kunstmaan\MediaPagePartBundle\Entity\VideoPagePart',
                ],
            ], $pagePartTypes
        );
    }

    /**
     * @return array
     */
    public function getPossiblePagePartTypes()
    {
        return $this->pagePartTypes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Page parts';
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return 'main';
    }

    /**
     * @return string
     */
    public function getWidgetTemplate()
    {
        return '';
    }
}
