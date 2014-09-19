<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * SatelliteOverviewPage
 *
 * @ORM\Table(name="{{ prefix }}_satellite_overview_page")
 * @ORM\Entity
 */
class SatelliteOverviewPage extends \Kunstmaan\NodeBundle\Entity\AbstractPage implements \Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=25, nullable=true)
     */
    private $type;

    /**
     * Set type
     *
     * @param string $type
     * @return SatelliteOverviewPage
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the default backend form type for this page
     *
     * @return \{{ namespace }}\Form\Pages\SatelliteOverviewPageAdminType
     */
    public function getDefaultAdminType()
    {
        return new \{{ namespace }}\Form\Pages\SatelliteOverviewPageAdminType();
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array();
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array('{{ bundle_name }}:satelliteoverviewpage');
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle_name }}:Pages:SatelliteOverviewPage/view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $renderContext)
    {
        $renderContext['satellites'] = array();

        if ($this->getType() != '') {
            $renderContext['satellites'] = $container->get('doctrine')
                ->getRepository('{{ bundle_name }}:Satellite')
                ->findBy(array('type' => $this->type), array('launched' => 'ASC'));
        }
    }
}