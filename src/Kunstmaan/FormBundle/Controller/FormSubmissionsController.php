<?php

namespace Kunstmaan\FormBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\AdminListBundle\AdminList\ExportList;
use Kunstmaan\AdminListBundle\Service\ExportService;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionExportListConfigurator;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The controller which will handle everything related with form pages and form submissions
 */
final class FormSubmissionsController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * The index action will use an admin list to list all the form pages
     */
    #[Route(path: '/', name: 'KunstmaanFormBundle_formsubmissions')]
    public function indexAction(Request $request): Response
    {
        $aclHelper = $this->container->get('kunstmaan_admin.acl.helper');

        /* @var AdminList $adminList */
        $adminList = $this->container->get('kunstmaan_adminlist.factory')->createList(
            new FormPageAdminListConfigurator($this->em, $aclHelper, PermissionMap::PERMISSION_VIEW)
        );
        $adminList->bindRequest($request);

        return $this->render('@KunstmaanAdminList/Default/list.html.twig', ['adminlist' => $adminList]);
    }

    /**
     * The list action will use an admin list to list all the form submissions related to the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     */
    #[Route(path: '/list/{nodeTranslationId}', requirements: ['nodeTranslationId' => '\d+'], name: 'KunstmaanFormBundle_formsubmissions_list', methods: ['GET', 'POST'])]
    public function listAction(Request $request, $nodeTranslationId): Response
    {
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)->find($nodeTranslationId);

        /** @var AdminList $adminList */
        $adminList = $this->container->get('kunstmaan_adminlist.factory')->createList(
            new FormSubmissionAdminListConfigurator($this->em, $nodeTranslation, $this->getParameter('kunstmaan_form.deletable_formsubmissions'))
        );
        $adminList->bindRequest($request);

        return $this->render('@KunstmaanForm/FormSubmissions/list.html.twig', [
            'nodetranslation' => $nodeTranslation,
            'adminlist' => $adminList,
        ]);
    }

    /**
     * The edit action will be used to edit a given submission.
     *
     * @param int $nodeTranslationId The node translation id
     * @param int $submissionId      The submission id
     */
    #[Route(path: '/list/{nodeTranslationId}/{submissionId}', requirements: ['nodeTranslationId' => '\d+', 'submissionId' => '\d+'], name: 'KunstmaanFormBundle_formsubmissions_list_edit', methods: ['GET', 'POST'])]
    public function editAction($nodeTranslationId, $submissionId): Response
    {
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)->find($nodeTranslationId);
        $formSubmission = $this->em->getRepository(FormSubmission::class)->find($submissionId);
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $deletableFormsubmission = $this->getParameter('kunstmaan_form.deletable_formsubmissions');

        /** @var AdminList $adminList */
        $adminList = $this->container->get('kunstmaan_adminlist.factory')->createList(
            new FormSubmissionAdminListConfigurator($this->em, $nodeTranslation, $deletableFormsubmission)
        );
        $adminList->bindRequest($request);

        return $this->render('@KunstmaanForm/FormSubmissions/edit.html.twig', [
            'nodetranslation' => $nodeTranslation,
            'formsubmission' => $formSubmission,
            'adminlist' => $adminList,
        ]);
    }

    /**
     * Export as CSV of all the form submissions for the given $nodeTranslationId
     *
     * @param int $nodeTranslationId
     *
     * @return Response
     */
    #[Route(path: '/export/{nodeTranslationId}.{_format}', requirements: ['nodeTranslationId' => '\d+', '_format' => 'csv|xlsx|ods'], name: 'KunstmaanFormBundle_formsubmissions_export', methods: ['GET'])]
    public function exportAction($nodeTranslationId, $_format)
    {
        /** @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $this->em->getRepository(NodeTranslation::class)->find($nodeTranslationId);
        $translator = $this->container->get('translator');

        /** @var ExportList $exportList */
        $configurator = new FormSubmissionExportListConfigurator($this->em, $nodeTranslation, $translator);
        $exportList = $this->container->get('kunstmaan_adminlist.factory')->createExportList($configurator);

        return $this->container->get('kunstmaan_adminlist.service.export')->getDownloadableResponse($exportList, $_format);
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     */
    #[Route(path: '/{id}/delete', requirements: ['id' => '\d+'], name: 'KunstmaanFormBundle_formsubmissions_delete', methods: ['POST'])]
    public function deleteAction(Request $request, $id)
    {
        $submission = $this->em->getRepository(FormSubmission::class)->find($id);

        $node = $this->em->getRepository(Node::class)->find($submission->getNode());
        $nt = $node->getNodeTranslation($request->getLocale());

        $configurator = new FormSubmissionAdminListConfigurator($this->em, $nt, $this->getParameter('kunstmaan_form.deletable_formsubmissions'));

        $slugifier = $this->container->get('kunstmaan_utilities.slugifier');
        if (!$this->isCsrfTokenValid('delete-' . $slugifier->slugify($configurator->getEntityClass()), $request->request->get('token'))) {
            $indexUrl = $configurator->getIndexUrl();

            return new RedirectResponse($this->generateUrl($indexUrl['path'], $indexUrl['params'] ?? []));
        }

        $this->denyAccessUnlessGranted(PermissionMap::PERMISSION_DELETE, $node);

        $url = $this->generateUrl('KunstmaanFormBundle_formsubmissions_list', ['nodeTranslationId' => $nt->getId()]);

        $fields = $this->em->getRepository(FormSubmissionField::class)->findBy(['formSubmission' => $submission]);

        try {
            foreach ($fields as $field) {
                $this->em->remove($field);
            }

            $this->em->remove($submission);
            $this->em->flush();

            $this->addFlash(
                FlashTypes::SUCCESS,
                $this->container->get('translator')->trans('formsubmissions.delete.flash.success')
            );
        } catch (\Exception $e) {
            $this->container->get('logger')->error($e->getMessage());
            $this->addFlash(
                FlashTypes::DANGER,
                $this->container->get('translator')->trans('formsubmissions.delete.flash.error')
            );
        }

        return new RedirectResponse($url);
    }

    public static function getSubscribedServices(): array
    {
        return [
            'kunstmaan_admin.acl.helper' => AclHelper::class,
            'kunstmaan_adminlist.factory' => AdminListFactory::class,
            'kunstmaan_adminlist.service.export' => ExportService::class,
            'request_stack' => RequestStack::class,
            'translator' => TranslatorInterface::class,
            'logger' => LoggerInterface::class,
            'kunstmaan_utilities.slugifier' => SlugifierInterface::class,
        ] + parent::getSubscribedServices();
    }
}
