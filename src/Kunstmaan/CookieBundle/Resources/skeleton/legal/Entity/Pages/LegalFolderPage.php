<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use {{ namespace }}\Form\Pages\LegalFolderPageAdminType;
use {{ namespace }}\Entity\Pages\LegalPage;

/**
 * LegalFolderPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}legal_folder_pages")
 */
class LegalFolderPage extends StructureNode
{
    /**
     * Returns the default backend form type for this page
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return LegalFolderPageAdminType::class;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [
            [
                'name' => 'Legal page',
                'class' => LegalPage::class,
            ],
        ];
    }
}
