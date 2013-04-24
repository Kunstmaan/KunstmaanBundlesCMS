<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Form\AbstractType;

use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use {{ namespace }}\Form\Pages\FormPageAdminType;
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
                'class' => "{{ namespace }}\Entity\Pages\ContentPage"
            ),
            array (
                'name' => 'FormPage',
                'class' => "{{ namespace }}\Entity\Pages\FormPage"
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
        return "{{ bundle.getName() }}:Pages\FormPage:view.html.twig";
    }
}
