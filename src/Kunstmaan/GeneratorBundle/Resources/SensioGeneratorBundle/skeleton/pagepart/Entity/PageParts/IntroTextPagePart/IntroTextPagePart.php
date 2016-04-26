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
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @param string $content
     *
     * @return {{ pagepart }}
     */
    public function setContent($content)
    {
	$this->content = $content;

	return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
	return $this->content;
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
