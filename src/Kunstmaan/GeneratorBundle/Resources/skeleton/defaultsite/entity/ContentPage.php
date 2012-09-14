<?php

namespace {{ namespace }}\Entity;

use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;

use {{ namespace }}\PagePartAdmin\BannerPagePartAdminConfigurator;

use {{ namespace }}\PagePartAdmin\ContentPagePagePartAdminConfigurator;

use Kunstmaan\AdminNodeBundle\Entity\HasNode;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Entity\DeepCloneableIFace;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\DemoBundle\Form\ContentPageAdminType;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Kunstmaan\SearchBundle\Entity\Indexable;
use Kunstmaan\AdminBundle\Modules\ClassLookup;

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