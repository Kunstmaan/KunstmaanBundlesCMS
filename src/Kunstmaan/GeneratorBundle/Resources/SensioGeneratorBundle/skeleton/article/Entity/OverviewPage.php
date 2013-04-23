<?php

namespace {{ namespace }}\Entity\Pages\{{ entity_class }};

use Doctrine\ORM\Mapping as ORM;
use {{ namespace }}\PagePartAdmin\{{ entity_class }}\{{ entity_class }}OverviewPagePagePartAdminConfigurator;
use Kunstmaan\ArticleBundle\Entity\AbstractArticleOverviewPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The article overview page which shows its articles
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}overviewpages")
 */
class {{ entity_class }}OverviewPage extends AbstractArticleOverviewPage
{

    /**
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new {{ entity_class }}OverviewPagePagePartAdminConfigurator());
    }

    /**
     * @param ContainerInterface $container
     * @param Request            $request
     * @param RenderContext      $context
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        parent::service($container, $request, $context);
        $em = $container->get('doctrine')->getManager();
        $repository = $em->getRepository('{{ bundle.getName() }}:Pages\{{ entity_class }}\{{ entity_class }}Page');
        $context['articles'] = $repository->getArticles();
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:Pages/{{ entity_class }}/{{ entity_class }}OverviewPage:view.html.twig";
    }

}
