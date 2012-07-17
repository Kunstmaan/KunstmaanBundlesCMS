<?php

namespace Kunstmaan\FormBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

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

/**
 * The formsubmissions admin controller
 */
class FormSubmissionsController extends Controller
{
	/**
	 * The index action
	 *
	 * @Route("/", name="KunstmaanFormBundle_formsubmissions")
	 * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
	 *
	 * @return array
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
     * The list
     * @param int $nodetranslationid
     *
     * @Route("/list/{nodetranslationid}", requirements={"nodetranslationid" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_list")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function listAction($nodetranslationid)
    {
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
     * The edit action
     * @param int $nodetranslationid The node translation id
     * @param int $submissionid      The submission id
     *
     * @Route("/list/{nodetranslationid}/{submissionid}", requirements={"nodetranslationid" = "\d+", "submissionid" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_list_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function editAction($nodetranslationid, $submissionid)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$request = $this->getRequest();
    	$nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->find($nodetranslationid);
    	$formSubmission = $em->getRepository('KunstmaanFormBundle:FormSubmission')->find($submissionid);

    	return array(
    			'nodetranslation' => $nodeTranslation,
    			'formsubmission' => $formSubmission,
    	);
    }

    /**
     * Export as CSV
     * @param int $nodetranslationid
     *
     * @Route("/export/{nodetranslationid}", requirements={"nodetranslationid" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_export")
     * @Method({"GET"})
     * @Template()
     */
    public function exportAction($nodetranslationid)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->find($nodetranslationid);

        $qb = $em->createQueryBuilder()
            ->select('fs')
            ->from('KunstmaanFormBundle:FormSubmission', 'fs')
            ->innerJoin('fs.node', 'n', 'WITH', 'fs.node = n.id')
            ->andWhere('n.id = ?1')
            ->setParameter(1, $nodeTranslation->getNode()->getId())
            ->addOrderBy('fs.created', 'DESC');
        
        $submissions = $qb->getQuery()->getResult();
        $response = $this->render('KunstmaanFormBundle:FormSubmissions:export.csv.twig', array('submissions' => $submissions));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="form-submissions.csv"');
        
        return $response;
    }
    
}
