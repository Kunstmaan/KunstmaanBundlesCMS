<?php

namespace {{ namespace }}\Entity\Pages\{{ entity_class }};

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractArticlePage;
use {{ namespace }}\Form\Pages\{{ entity_class }}\{{ entity_class }}PageAdminType;
use {{ namespace }}\PagePartAdmin\{{ entity_class }}\{{ entity_class }}PagePagePartAdminConfigurator;
use Symfony\Component\Form\AbstractType;

/**
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\Pages\{{ entity_class }}\{{ entity_class }}PageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}pages")
 */
class {{ entity_class }}Page extends AbstractArticlePage
{

    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new {{ entity_class }}PageAdminType();
    }

    /**
     * @return array
     */
    public function getPagePartAdminConfigurations()
    {
        return array(new {{ entity_class }}PagePagePartAdminConfigurator());
    }
}
