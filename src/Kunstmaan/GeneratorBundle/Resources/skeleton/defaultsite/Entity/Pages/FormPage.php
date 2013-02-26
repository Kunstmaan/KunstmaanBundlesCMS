<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use {{ namespace }}\Form\FormPageAdminType;
use {{ namespace }}\PagePartAdmin\FormPagePagePartAdminConfigurator;
use {{ namespace }}\PagePartAdmin\BannerPagePartAdminConfigurator;

/**
 * FormPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}form_pages")
 */
class FormPage extends AbstractFormPage
{

    /**
     * Returns the default backend form type for this form
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new FormPageAdminType();
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array(
            array(
                'name' => 'ContentPage',
                'class' => "{{ namespace }}\Entity\ContentPage"
            ),
            array (
                'name' => 'FormPage',
                'class' => "{{ namespace }}\Entity\FormPage"
            )
        );
    }

    /**
     * @return array
     */
    public function getPagePartAdminConfigurations()
    {
        return array(
            new FormPagePagePartAdminConfigurator(),
            new BannerPagePartAdminConfigurator()
        );
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:FormPage:view.html.twig";
    }
}
