<?php

namespace Kunstmaan\FormBundle\Entity;

use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;

/**
 * The Abstract ORM FormPage
 */
abstract class AbstractFormPage extends AbstractPage
{
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text", nullable=true)
     */
    protected $thanks;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $subject;

    /**
     * @ORM\Column(type="string", name="from_email", nullable=true)
     * @Assert\Email()
     */
    protected $fromEmail;

    /**
     * @ORM\Column(type="string", name="to_email", nullable=true)
     */
    protected $toEmail;

    /**
     * @param string $thanks
     */
    public function setThanks($thanks)
    {
        $this->thanks = $thanks;
    }

    /**
     * @return string
     */
    public function getThanks()
    {
        return $this->thanks;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getToEmail()
    {
        return $this->toEmail;
    }

    /**
     * @param string $toEmail
     */
    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @param ContainerInterface $container The Container
     * @param Request            $request   The Request
     * @param array              &$result   The Result array
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function service(ContainerInterface $container, Request $request, &$result)
    {
        $thanksParam = $request->get('thanks');
        if (!empty($thanksParam)) {
            $result["thanks"] = true;
        } else {
            $formbuilder = $container->get('form.factory')->createBuilder('form');
            $em = $container->get('doctrine')->getEntityManager();
            $pageparts = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($this, $this->getFormElementsContext());
            $fields = array();
            foreach ($pageparts as $pagepart) {
                if ($pagepart instanceof FormAdaptorInterface) {
                    $pagepart->adaptForm($formbuilder, $fields);
                }
            }
            $form = $formbuilder->getForm();
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $formsubmission = new FormSubmission();
                    $formsubmission->setIpAddress($request->getClientIp());
                    $formsubmission->setNode($em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($this));
                    $formsubmission->setLang($locale = $request->getLocale());
                    $em->persist($formsubmission);
                    foreach ($fields as &$field) {
                        $field->setSubmission($formsubmission);
                        $field->onValidPost($form, $formbuilder, $request, $container);
                        $em->persist($field);
                    }
                    $em->flush();
                    $em->refresh($formsubmission);

                    $from = $this->getFromEmail();
                    $to = $this->getToEmail();
                    $subject = $this->getSubject();
                    if (!empty($from) && !empty($to) && !empty($subject)) {
                        $container->get('form.mailer')->sendContactMail($formsubmission, $from, $to, $subject);
                    }

                    return new RedirectResponse($container->get('router')->generate('_slug', array(
                        'url' => $result['slug'],
                        '_locale' => $result['nodetranslation']->getLang(),
                        'thanks' => true
                    )));
                }
            }
            $result["frontendform"] = $form->createView();
            $result["frontendformobject"] = $form;
        }
    }

    /**
     * @return array
     */
    abstract public function getPagePartAdminConfigurations();

    /**
     * @return string
     */
    abstract public function getDefaultView();

    /**
     * {@inheritdoc}
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
