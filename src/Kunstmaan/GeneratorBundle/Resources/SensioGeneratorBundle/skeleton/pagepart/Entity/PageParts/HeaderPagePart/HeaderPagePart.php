<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * {{ pagepart }}
 *
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 * @ORM\Entity
 */
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @ORM\Column(name="niv", type="integer", nullable=true)
     * @Assert\NotBlank(message="headerpagepart.niv.not_blank")
     */
    private $niv;

    /**
     * @ORM\Column(name="title", type="string", nullable=true)
     * @Assert\NotBlank(message="headerpagepart.title.not_blank")
     */
    private $title;

    /**
     * @var array Supported header sizes
     */
    public static $supportedHeaders = array(1, 2, 3, 4, 5, 6);

    /**
     * Set niv
     *
     * @param int $niv
     *
     * @return {{ pagepart }}
     */
    public function setNiv($niv)
    {
	$this->niv = $niv;

	return $this;
    }

    /**
     * Get niv
     *
     * @return int
     */
    public function getNiv()
    {
	return $this->niv;
    }

    /**
     * @param string $title
     *
     * @return {{ pagepart }}
     */
    public function setTitle($title)
    {
	$this->title = $title;

	return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
	return $this->title;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return '{{ bundle }}:PageParts:{{ pagepart }}/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return {{ adminType }}
     */
    public function getDefaultAdminType()
    {
	return new {{ adminType }}();
    }
}
