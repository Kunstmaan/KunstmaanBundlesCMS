<?php

namespace Kunstmaan\FormBundle\Controller;

use Kunstmaan\AdminBundle\Form\EditUserType;
use Kunstmaan\AdminBundle\Form\EditGroupType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Form\UserType;
use Kunstmaan\AdminBundle\Form\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class FormSubmissionsController extends Controller
{
	/**
	 * @Route("/", name="KunstmaanFormBundle_formsubmissions")
	 * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
	 */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$request = $this->getRequest();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$formpagesadminlist = $this->get("adminlist.factory")->createList(new FormPageAdminListConfigurator($user, 'read'), $em);
    	$formpagesadminlist->bindRequest($request);

    	return array(
    			'adminlist' => $formpagesadminlist
    	);
    }

    /**
     * @Route("/list/{nodetranslationid}", requirements={"nodetranslationid" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_list")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function listAction($nodetranslationid) {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->find($nodetranslationid);
        $adminlist = $this->get("adminlist.factory")->createList(new FormSubmissionAdminListConfigurator($nodeTranslation), $em);
        $adminlist->bindRequest($request);

        return array(
        	'nodetranslation' => $nodeTranslation,
            'adminlist' => $adminlist
        );
    }



    /**
     * @Route("/list/{nodetranslationid}/{submissionid}", requirements={"nodetranslationid" = "\d+", "submissionid" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_list_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($nodetranslationid, $submissionid) {
    	$em = $this->getDoctrine()->getEntityManager();
    	$request = $this->getRequest();
    	$nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->find($nodetranslationid);
    	$formSubmission = $em->getRepository('KunstmaanFormBundle:FormSubmission')->find($submissionid);

    	return array(
    			'nodetranslation' => $nodeTranslation,
    			'formsubmission' => $formSubmission,
    	);
    }


}
