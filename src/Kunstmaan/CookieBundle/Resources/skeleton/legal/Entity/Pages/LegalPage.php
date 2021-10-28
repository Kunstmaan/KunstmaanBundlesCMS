<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\CookieBundle\Controller\LegalController;
use {{ namespace }}\Form\Pages\LegalPageAdminType;

/**
 * LegalFolderPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}legal_pages")
 */
class LegalPage extends AbstractPage implements HasPageTemplateInterface, SlugActionInterface
{
    /**
     * Returns the default backend form type for this page
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return LegalPageAdminType::class;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return [
            '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}legal_header',
            '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}legal_main',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}legalpage'];
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/LegalPage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getControllerAction()
    {
        {% if not isV4 %}return 'KunstmaanCookieBundle:Legal:legalPage';{% else %}return LegalController::class.'::legalPageAction';{%endif%}
    }
}
