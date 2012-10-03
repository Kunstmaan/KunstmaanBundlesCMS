<?php

namespace {{ namespace }}\Entity;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\AdminBundle\Entity\DeepCloneableIFace;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HasNode;
use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\FormBundle\Entity\FormAdaptorIFace;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\SearchBundle\Entity\Indexable;

use {{ namespace }}\Form\ContentPageAdminType;
use {{ namespace }}\PagePartAdmin\FormPagePagePartAdminConfigurator;
use {{ namespace }}\PagePartAdmin\BannerPagePartAdminConfigurator;
use {{ namespace }}\PagePartAdmin\ContentPagePagePartAdminConfigurator;

/**
 * FormPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}formpage")
 * @ORM\HasLifecycleCallbacks()
 */
class FormPage extends AbstractFormPage
{

    public function setThanks($thanks)
    {
        $this->thanks = $thanks;
    }

    public function getThanks()
    {
        return $this->thanks;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getDefaultAdminType()
    {
        return new FormPageAdminType();
    }

    public function isOnline()
    {
        return true;
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

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getPossiblePermissions()
    {
        return $this->possiblePermissions;
    }

    public function getPossibleChildPageTypes()
    {
        $array[] = array('name' => 'ContentPage', 'class' => "{{ namespace }}\Entity\ContentPage");
        $array[] = array('name' => 'FormPage', 'class' => "{{ namespace }}\Entity\FormPage");

        return $array;
    }

    public function getPagePartAdminConfigurations()
    {
        return array(new FormPagePagePartAdminConfigurator(), new BannerPagePartAdminConfigurator());
    }

    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:FormPage:view.html.twig";
    }
}
