<?php

namespace Kunstmaan\FormBundle\Entity;

use ArrayObject;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This is an abstract ORM form page. With this page it's possible to create forms using a mix of form page parts and
 * regular page parts. When the form is submitted a FormSubmission will be generated and a thank you page is shown.
 * Furthermore it's possible to configure an administrative email to be send when a form is submitted with in it an
 * overview of all the submitted fields.
 */
abstract class AbstractFormPage extends AbstractPage
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
     */
    public function setThanks($thanks)
    {
        $this->thanks = $thanks;
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
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
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
     */
    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;
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
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    /**
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
            'url' => $result['slug'],
            '_locale' => $nodeTranslation->getLang(),
            'thanks' => true
        ));
    }

    /**
     * This service function will handle the creation of the form and submitting the form
     *
     * @param ContainerInterface $container The Container
     * @param Request            $request   The Request
     * @param RenderContext      $context   The Render context
     *
     * @return null|RedirectResponse|void
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        $thanksParam = $request->get('thanks');
        if (!empty($thanksParam)) {
            $result["thanks"] = true;

            return null;
        }

        return $container->get('kunstmaan_form.form_handler')->handleForm($this, $request, $context);
    }

    /**
     * Returns the page part configurations which specify which page parts can be added to this form
     *
     * @return array
     */
    abstract public function getPagePartAdminConfigurations();

    /**
     * Returns the default view of this form
     *
     * @return string
     */
    abstract public function getDefaultView();

    /**
     * Returns the default backend form type for this form
     *
     * @return AbstractFormPageAdminType
     */
    public function getDefaultAdminType()
    {
        return new AbstractFormPageAdminType();
    }

    /**
     * @return string
     */
    public function getFormElementsContext()
    {
        return "main";
    }

}
