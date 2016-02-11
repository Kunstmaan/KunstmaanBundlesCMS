<?php

namespace Kunstmaan\FormBundle\Entity;

use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\FormBundle\Helper\FormPageInterface;

use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;

/**
 * This is an abstract ORM form page. With this page it's possible to create forms using a mix of form page parts and
 * regular page parts. When the form is submitted a FormSubmission will be generated and a thank you page is shown.
 * Furthermore it's possible to configure an administrative email to be send when a form is submitted with in it an
 * overview of all the submitted fields.
 */
abstract class AbstractFormPage extends AbstractPage implements FormPageInterface, HasPagePartsInterface, SlugActionInterface
{
    /**
     * The thank you text to be shown when the form was successfully submitted
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="text", nullable=true)
     */
    protected $thanks;

    /**
     * The subject of the administrative email
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $subject;

    /**
     * The sender of the administrative email
     *
     * @ORM\Column(type="string", name="from_email", nullable=true)
     * @Assert\Email()
     */
    protected $fromEmail;

    /**
     * The recipient of the administrative email
     *
     * @ORM\Column(type="string", name="to_email", nullable=true)
     */
    protected $toEmail;

    /**
     * Sets the thanks text, shown when the form was successfully submitted
     *
     * @param string $thanks
     *
     * @return AbstractFormPage
     */
    public function setThanks($thanks)
    {
        $this->thanks = $thanks;

        return $this;
    }

    /**
     * Get the thanks text, shown when the form was successfully submitted
     *
     * @return string
     */
    public function getThanks()
    {
        return $this->thanks;
    }

    /**
     * Get the subject of the administrative email
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the subject of the administrative email
     *
     * @param string $subject
     *
     * @return AbstractFormPage
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the email address of the recipient from the administrative email
     *
     * @return string
     */
    public function getToEmail()
    {
        return $this->toEmail;
    }

    /**
     * Set the email address of the recipient from the administrative email
     *
     * @param string $toEmail
     *
     * @return AbstractFormPage
     */
    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    /**
     * Get the email address of the sender of the administrative email
     *
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * Sets the email address of the sender of the administrative email
     *
     * @param string $fromEmail
     *
     * @return AbstractFormPage
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * Generate the url of the thank you page
     *
     * @param RouterInterface $router  The router
     * @param RenderContext   $context The render context
     *
     * @return string
     */
    public function generateThankYouUrl(RouterInterface $router, RenderContext $context)
    {
        /* @var $nodeTranslation NodeTranslation */
        $nodeTranslation = $context['nodetranslation'];

        return $router->generate('_slug', array(
            'url' => $context['slug'],
            '_locale' => $nodeTranslation->getLang(),
            'thanks' => true
        ));
    }

    /**
     * Returns the default backend form type for this form
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new AbstractFormPageAdminType();
    }

    /**
     * Get the page part context used for the form
     *
     * @return string
     */
    public function getFormElementsContext()
    {
        return "main";
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        return 'KunstmaanFormBundle:AbstractFormPage:service';
    }

}
