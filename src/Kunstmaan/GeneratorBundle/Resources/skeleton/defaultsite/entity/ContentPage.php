<?php

namespace {{ namespace }}\Entity;

use Symfony\Component\HttpFoundation\Request;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

use Kunstmaan\AdminBundle\Entity\DeepCloneableIFace;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HasNode;
use Kunstmaan\SearchBundle\Entity\Indexable;

use {{ namespace }}\Form\ContentPageAdminType;
use {{ namespace }}\PagePartAdmin\BannerPagePartAdminConfigurator;
use {{ namespace }}\PagePartAdmin\ContentPagePagePartAdminConfigurator;

/**
 * ContentPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="contentpage")
 * @ORM\HasLifecycleCallbacks()
 */
class ContentPage extends AbstractPage
{

    public function getDefaultAdminType()
    {
        return new ContentPageAdminType();
    }

    public function getContentForIndexing($container, $entity)
    {
        $renderer = $container->get('templating');
        $em = $container
            ->get('doctrine')
            ->getEntityManager();

        $pageparts = $em
            ->getRepository('KunstmaanPagePartBundle:PagePartRef')
            ->getPageParts($this);

        $classname = ClassLookup::getClassName($this);

        $view = 'KunstmaanDemoBundle:Elastica:' . $classname . '.elastica.twig';

        $temp = $renderer->render($view, array('page' => $this, 'pageparts' => $pageparts));

        return strip_tags($temp);
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleChildPageTypes()
    {
        $array[] = array('name' => 'ContentPage', 'class'=> "{{ namespace }}\Entity\ContentPage");

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function deepClone(EntityManager $em)
    {
        $newpage = new ContentPage();
        $newpage->setTitle($this->getTitle());
        $em->persist($newpage);
        $em->flush();
        $em
            ->getRepository('KunstmaanPagePartBundle:PagePartRef')
            ->copyPageParts($em, $this, $newpage, $context = "main");

        return $newpage;
    }

    /**
     * {@inheritdoc}
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new ContentPagePagePartAdminConfigurator(), new BannerPagePartAdminConfigurator());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:ContentPage:view.html.twig";
    }
}