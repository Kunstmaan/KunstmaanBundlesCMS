<?php

namespace {{ namespace }}\Entity;

use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;
use {{ namespace }}\PagePartAdmin\HomePagePagePartAdminConfigurator;

use Kunstmaan\AdminNodeBundle\Entity\HasNode;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Entity\DeepCloneableIFace;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use {{ namespace }}\Form\HomePageAdminType;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Kunstmaan\SearchBundle\Entity\Indexable;
use Kunstmaan\AdminBundle\Modules\ClassLookup;

/**
 * HomePage
 *
 * @ORM\Entity()
 * @ORM\Table(name="demohomepage")
 * @ORM\HasLifecycleCallbacks()
 */
class HomePage extends AbstractPage
{

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new HomePageAdminType();
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

        $view = '{{ bundle.getName() }}:Elastica:' . $classname . '.elastica.twig';

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
        $newpage = new HomePage();
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
        return array(new HomePagePagePartAdminConfigurator());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:HomePage:view.html.twig";
    }
}