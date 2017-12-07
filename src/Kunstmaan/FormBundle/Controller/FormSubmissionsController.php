<?php

namespace Kunstmaan\FormBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\AclHelperTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\AdminListFactoryTrait;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\TranslatorTrait;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\ExportList;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionExportListConfigurator;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * The controller which will handle everything related with form pages and form submissions
 */
class FormSubmissionsController extends AbstractController
{
    use TranslatorTrait,
        AclHelperTrait,
        AdminListFactoryTrait;

    /**
     * The index action will use an admin list to list all the form pages
     *
     * @Route("/", name="KunstmaanFormBundle_formsubmissions")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        /* @var EntityManager $em */
        $em        = $this->getDoctrine()->getManager();
        $aclHelper = $this->getAclHelper();

        /* @var AdminList $adminList */
        $adminList = $this->getAdminListFactory()->createList(
            new FormPageAdminListConfigurator($em, $aclHelper, PermissionMap::PERMISSION_VIEW),
            $em
        );
        $adminList->bindRequest($request);

        return array('adminlist' => $adminList);
    }

    /**
     * The list action will use an admin list to list all the form submissions related to the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     *
     * @Route("/list/{nodeTranslationId}", requirements={"nodeTranslationId" = "\d+"},
     *                                     name="KunstmaanFormBundle_formsubmissions_list")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function listAction(Request $request, $nodeTranslationId)
    {
        $em              = $this->getDoctrine()->getManager();
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);

        /** @var AdminList $adminList */
        $adminList = $this->getAdminListFactory()->createList(
            new FormSubmissionAdminListConfigurator($em, $nodeTranslation),
            $em
        );
        $adminList->bindRequest($request);

        return array('nodetranslation' => $nodeTranslation, 'adminlist' => $adminList);
    }

    /**
     * The edit action will be used to edit a given submission
     *
     * @param int $nodeTranslationId The node translation id
     * @param int $submissionId      The submission id
     *
     * @Route("/list/{nodeTranslationId}/{submissionId}", requirements={"nodeTranslationId" = "\d+", "submissionId" =
     *                                                    "\d+"}, name="KunstmaanFormBundle_formsubmissions_list_edit")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function editAction($nodeTranslationId, $submissionId)
    {
        $em                   = $this->getDoctrine()->getManager();
        $nodeTranslation      = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);
        $formSubmission       = $em->getRepository('KunstmaanFormBundle:FormSubmission')->find($submissionId);

        return array(
            'nodetranslation' => $nodeTranslation,
            'formsubmission' => $formSubmission
        );
    }

    /**
     * Export as CSV of all the form submissions for the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     *
     * @Route("/export/{nodeTranslationId}.{_format}", requirements={"nodeTranslationId" = "\d+","_format" =
     *                                                 "csv|xlsx|ods"}, name="KunstmaanFormBundle_formsubmissions_export")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function exportAction($nodeTranslationId, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);
        $translator      = $this->getTranslator();

        /** @var ExportList $exportList */
        $configurator = new FormSubmissionExportListConfigurator($em, $nodeTranslation, $translator);
        $exportList   = $this->getAdminListFactory()->createExportList($configurator);

        return $this->container->get('kunstmaan_adminlist.service.export')->getDownloadableResponse($exportList, $_format);
    }
}
