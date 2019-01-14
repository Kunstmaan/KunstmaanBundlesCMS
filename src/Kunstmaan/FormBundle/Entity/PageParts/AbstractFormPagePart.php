<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Abstract version of a form page part
 */
abstract class AbstractFormPagePart extends AbstractPagePart implements FormAdaptorInterface
{
    /**
     * The label
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $label;

    /**
     * Returns a unique id for the current page part
     *
     * @return string
     */
    public function getUniqueId()
    {
        return  str_replace('\\', '', ClassLookup::getClass($this)) . $this->id; //TODO
    }

    /**
     * Set the label used for this page part
     *
     * @param int $label
     *
     * @return AbstractFormPagePart
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the label used for this page part
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the view used in the backend
     *
     * @return string
     */
    public function getAdminView()
    {
        return 'KunstmaanFormBundle:AbstractFormPagePart:admin-view.html.twig';
    }
}
