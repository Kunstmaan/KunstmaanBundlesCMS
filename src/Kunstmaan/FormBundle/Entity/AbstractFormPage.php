<?php

namespace Kunstmaan\FormBundle\Entity;

use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;

use Kunstmaan\AdminNodeBundle\Entity\AbstractPage;

use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\FormBundle\Entity\FormSubmission;

use Doctrine\ORM\EntityManager;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The Abstract ORM FormPage
 */
abstract class AbstractFormPage extends AbstractPage
{
    /**
     * @ORM\Column(type="text", nullable="true")
     */
    protected $thanks;

    /**
     * @ORM\Column(type="string", nullable="true")
     */
    protected $subject;

    /**
     * @ORM\Column(type="string", name="from_email", nullable="true")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $fromEmail;

    /**
     * @ORM\Column(type="string", name="to_email", nullable="true")
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
     * {@inheritdoc}
     */
    public function deepClone(EntityManager $em)
    {
        $newpage = new FormPage();
        $newpage->setTitle($this->getTitle());
        $em->persist($newpage);
        $em->flush();
        $ppconfigurations = $this->getPagePartAdminConfigurations();
        foreach ($ppconfigurations as $ppconfiguration) {
            $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->copyPageParts($em, $this, $newpage, $ppconfiguration->getDefaultContext());
        }

        return $newpage;
    }

    /**
     * {@inheritdoc}
     */
    public function service($container, Request $request, &$result)
    {
        $formbuilder = $container->get('form.factory')->createBuilder('form');
        $em = $container->get('doctrine')->getEntityManager();
        $pageparts = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($this, "main");
        $fields = array();
        foreach ($pageparts as $pagepart) {
            if ($pagepart instanceof FormAdaptorInterface) {
                $pagepart->adaptForm($formbuilder, $fields);
            }
        }
        $form = $formbuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $formsubmission = new FormSubmission();
                $formsubmission->setIpAddress($request->getClientIp());
                $formsubmission->setNode($em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($this));
                $formsubmission->setLang($locale = $request->getSession()->getLocale());
                $em->persist($formsubmission);
                $em->flush();
                foreach ($fields as &$field) {
                    $field->setSubmission($formsubmission);
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
                $result["thanks"] = true;

                return;
            }
        }
        $result["frontendform"] = $form->createView();
    }

    /**
     * @return array
     */
    public abstract function getPagePartAdminConfigurations();

    /**
     * @return string
     */
    public abstract function getDefaultView();

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new AbstractFormPageAdminType();
    }

}
