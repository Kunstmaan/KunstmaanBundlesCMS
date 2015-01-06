<?php

namespace Kunstmaan\FormBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\ExportList;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionExportListConfigurator;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * The controller which will handle everything related with form pages and form submissions
 */
class FormSubmissionsController extends Controller
{
    /**
     * The index action will use an admin list to list all the form pages
     *
     * @Route("/", name="KunstmaanFormBundle_formsubmissions")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
        /* @var $adminList AdminList */
        $adminList = $this->get('kunstmaan_adminlist.factory')->createList(new FormPageAdminListConfigurator($em, $aclHelper, PermissionMap::PERMISSION_VIEW), $em);
        $adminList->bindRequest($request);

        return array('adminlist' => $adminList);
    }

    /**
     * The list action will use an admin list to list all the form submissions related to the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     *
     * @Route("/list/{nodeTranslationId}", requirements={"nodeTranslationId" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_list")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function listAction($nodeTranslationId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);
        /* @var $adminList AdminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList(new FormSubmissionAdminListConfigurator($em, $nodeTranslation), $em);
        $adminList->bindRequest($request);

        return array('nodetranslation' => $nodeTranslation, 'adminlist' => $adminList);
    }

    /**
     * The edit action will be used to edit a given submission
     *
     * @param int $nodeTranslationId The node translation id
     * @param int $submissionId The submission id
     *
     * @Route("/list/{nodeTranslationId}/{submissionId}", requirements={"nodeTranslationId" = "\d+", "submissionId" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_list_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function editAction($nodeTranslationId, $submissionId)
    {
        $em = $this->getDoctrine()->getManager();
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);
        $formSubmission = $em->getRepository('KunstmaanFormBundle:FormSubmission')->find($submissionId);

        return array('nodetranslation' => $nodeTranslation, 'formsubmission' => $formSubmission,);
    }

    /**
     * Export as CSV of all the form submissions for the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     *
     * @Route("/export/{nodeTranslationId}.{_format}", requirements={"nodeTranslationId" = "\d+","_format" = "csv|xlsx"}, name="KunstmaanFormBundle_formsubmissions_export")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function exportAction($nodeTranslationId, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $nodeTranslation NodeTranslation */
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);
        $translator = $this->get('translator');

        /* @var ExportList $exportlist */
        $configurator = new FormSubmissionExportListConfigurator($em, $nodeTranslation, $translator);
        $exportlist = $this->get("kunstmaan_adminlist.factory")->createExportList($configurator);

        return $this->get("kunstmaan_adminlist.service.export")->getDownloadableResponse($exportlist, $_format);
    }
}
