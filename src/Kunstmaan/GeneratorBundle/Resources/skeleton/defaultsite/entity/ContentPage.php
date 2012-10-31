<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use {{ namespace }}\Form\ContentPageAdminType;
use {{ namespace }}\PagePartAdmin\BannerPagePartAdminConfigurator;
use {{ namespace }}\PagePartAdmin\ContentPagePagePartAdminConfigurator;

/**
 * ContentPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}content_pages")
 */
class ContentPage extends AbstractPage implements HasPagePartsInterface
{

    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new ContentPageAdminType();
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array (
            array(
                'name' => 'ContentPage',
                'class'=> "{{ namespace }}\Entity\ContentPage"
            )
        );
    }

    /**
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new ContentPagePagePartAdminConfigurator(), new BannerPagePartAdminConfigurator());
    }

    /**
     * return string
     */
    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:ContentPage:view.html.twig";
    }
}