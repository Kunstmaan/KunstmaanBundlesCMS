<?php

namespace Kunstmaan\FormBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\ExportList;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionExportListConfigurator;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The controller which will handle everything related with form pages and form submissions
 */
class FormSubmissionsController extends Controller
{
    /**
     * The index action will use an admin list to list all the form pages
     *
     * @Route("/", name="KunstmaanFormBundle_formsubmissions")
     * @Template("@KunstmaanAdminList/Default/list.html.twig")
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $aclHelper = $this->container->get('kunstmaan_admin.acl.helper');

        /* @var AdminList $adminList */
        $adminList = $this->get('kunstmaan_adminlist.factory')->createList(
            new FormPageAdminListConfigurator($em, $aclHelper, PermissionMap::PERMISSION_VIEW)
        );
        $adminList->bindRequest($request);

        return ['adminlist' => $adminList];
    }

    /**
     * The list action will use an admin list to list all the form submissions related to the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     *
     * @Route("/list/{nodeTranslationId}", requirements={"nodeTranslationId" = "\d+"},
     *                                     name="KunstmaanFormBundle_formsubmissions_list", methods={"GET", "POST"})
     * @Template("@KunstmaanForm/FormSubmissions/list.html.twig")
     *
     * @return array
     */
    public function listAction(Request $request, $nodeTranslationId)
    {
        $em = $this->getDoctrine()->getManager();
        $nodeTranslation = $em->getRepository(NodeTranslation::class)->find($nodeTranslationId);

        /** @var AdminList $adminList */
        $adminList = $this->get('kunstmaan_adminlist.factory')->createList(
            new FormSubmissionAdminListConfigurator($em, $nodeTranslation, $this->getParameter('kunstmaan_form.deletable_formsubmissions'))
        );
        $adminList->bindRequest($request);

        return ['nodetranslation' => $nodeTranslation, 'adminlist' => $adminList];
    }

    /**
     * The edit action will be used to edit a given submission.
     *
     * @param int $nodeTranslationId The node translation id
     * @param int $submissionId      The submission id
     *
     * @Route("/list/{nodeTranslationId}/{submissionId}", requirements={"nodeTranslationId" = "\d+", "submissionId" =
     *                                                    "\d+"}, name="KunstmaanFormBundle_formsubmissions_list_edit", methods={"GET", "POST"})
     * @Template("@KunstmaanForm/FormSubmissions/edit.html.twig")
     *
     * @return array
     */
    public function editAction($nodeTranslationId, $submissionId)
    {
        $em = $this->getDoctrine()->getManager();
        $nodeTranslation = $em->getRepository(NodeTranslation::class)->find($nodeTranslationId);
        $formSubmission = $em->getRepository(FormSubmission::class)->find($submissionId);
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $deletableFormsubmission = $this->getParameter('kunstmaan_form.deletable_formsubmissions');

        /** @var AdminList $adminList */
        $adminList = $this->get('kunstmaan_adminlist.factory')->createList(
            new FormSubmissionAdminListConfigurator($em, $nodeTranslation, $deletableFormsubmission)
        );
        $adminList->bindRequest($request);

        return [
            'nodetranslation' => $nodeTranslation,
            'formsubmission' => $formSubmission,
            'adminlist' => $adminList,
        ];
    }

    /**
     * Export as CSV of all the form submissions for the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     *
     * @Route("/export/{nodeTranslationId}.{_format}", requirements={"nodeTranslationId" = "\d+","_format" =
     *                                                 "csv|xlsx|ods"}, name="KunstmaanFormBundle_formsubmissions_export", methods={"GET"})
     *
     * @return Response
     */
    public function exportAction($nodeTranslationId, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $em->getRepository(NodeTranslation::class)->find($nodeTranslationId);
        $translator = $this->get('translator');

        /** @var ExportList $exportList */
        $configurator = new FormSubmissionExportListConfigurator($em, $nodeTranslation, $translator);
        $exportList = $this->get('kunstmaan_adminlist.factory')->createExportList($configurator);

        return $this->get('kunstmaan_adminlist.service.export')->getDownloadableResponse($exportList, $_format);
    }

    /**
     * @Route(
     *      "/{id}/delete",
     *      requirements={"id" = "\d+"},
     *      name="KunstmaanFormBundle_formsubmissions_delete",
     *      methods={"POST"}
     * )
     *
     * @param int $id
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $submission = $em->getRepository(FormSubmission::class)->find($id);

        $node = $em->getRepository(Node::class)->find($submission->getNode());
        $nt = $node->getNodeTranslation($request->getLocale());

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_DELETE, $node);

        $url = $this->get('router')->generate(
            'KunstmaanFormBundle_formsubmissions_list',
            ['nodeTranslationId' => $nt->getId()]
        );

        $fields = $em->getRepository(FormSubmissionField::class)->findBy(['formSubmission' => $submission]);

        try {
            foreach ($fields as $field) {
                $em->remove($field);
            }

            $em->remove($submission);
            $em->flush();

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->get('translator')->trans('formsubmissions.delete.flash.success')
            );
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $this->addFlash(
                FlashTypes::DANGER,
                $this->get('translator')->trans('formsubmissions.delete.flash.error')
            );
        }

        return new RedirectResponse($url);
    }
}
