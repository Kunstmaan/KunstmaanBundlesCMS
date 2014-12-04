<?php

namespace {{ namespace }}\Entity\{{ entity_class }};

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractArticlePage;
use {{ namespace }}\Entity\{{ entity_class }}\{{ entity_class }}Author;
use {{ namespace }}\Form\{{ entity_class }}\{{ entity_class }}PageAdminType;
use {{ namespace }}\PagePartAdmin\{{ entity_class }}\{{ entity_class }}PagePagePartAdminConfigurator;
use Symfony\Component\Form\AbstractType;

/**
 * @ORM\Entity(repositoryClass="{{ namespace }}\Repository\{{ entity_class }}\{{ entity_class }}PageRepository")
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_pages")
 * @ORM\HasLifecycleCallbacks
 */
class {{ entity_class }}Page extends AbstractArticlePage
{
    /**
     * @var {{ entity_class }}Author
     *
     * @ORM\ManyToOne(targetEntity="{{ entity_class }}Author")
     * @ORM\JoinColumn(name="{{ entity_class|lower }}_author_id", referencedColumnName="id")
     */
    protected $author;

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

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:{{ entity_class }}/{{ entity_class }}Page:view.html.twig';
    }

    /**
     * Before persisting this entity, check the date.
     * When no date is present, fill in current date and time.
     *
     * @ORM\PrePersist
     */
    public function _prePersist()
    {
        // Set date to now when none is set
        if ($this->date == null) {
            $this->setDate(new \DateTime());
        }
    }
}
