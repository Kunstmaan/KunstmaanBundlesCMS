<?php

namespace Kunstmaan\FormBundle\Controller;

use Ddeboer\DataImport\Writer\CsvWriter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Kunstmaan\FormBundle\Entity\FormSubmission;

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
     * @param int $submissionId      The submission id
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
     * @Route("/export/{nodeTranslationId}", requirements={"nodeTranslationId" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_export")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function exportAction($nodeTranslationId)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $nodeTranslation NodeTranslation */
        $nodeTranslation = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodeTranslationId);

        $tmpFilename = tempnam('/tmp', 'cb_csv_');
        $file = new \SplFileObject($tmpFilename);
        $writer = new CsvWriter($file);

        /* @var $qb QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->select('fs')
           ->from('KunstmaanFormBundle:FormSubmission', 'fs')
           ->innerJoin('fs.node', 'n', 'WITH', 'fs.node = n.id')
           ->andWhere('n.id = :node')
           ->setParameter('node', $nodeTranslation->getNode()->getId())
           ->addOrderBy('fs.created', 'DESC');
        $iterableResult = $qb->getQuery()->iterate();
        $isHeaderWritten = false;
        $translator = $this->get('translator');

        foreach ($iterableResult as $row) {
            /* @var $submission FormSubmission */
            $submission = $row[0];

            // Write header info
            if (!$isHeaderWritten) {
                $header = array($translator->trans("Id"), $translator->trans("Date"), $translator->trans("Language"));
                foreach ($submission->getFields() as $field) {
                    $header[] = mb_convert_encoding($translator->trans($field->getLabel()), 'ISO-8859-1', 'UTF-8');
                }
                $writer->writeItem($header);
                $isHeaderWritten = true;
            }

            // Write row data
            $data = array($submission->getId(), $submission->getCreated()->format('d/m/Y H:i:s'), $submission->getLang());
            foreach ($submission->getFields() as $field) {
            $data[] = mb_convert_encoding($field->__toString(), 'ISO-8859-1', 'UTF-8');
            }
            $writer->writeItem($data);
            $em->detach($submission);
        }
        $writer->finish();

        $response = new Response(file_get_contents($tmpFilename));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="form-submissions.csv"');
        unlink($tmpFilename);

        return $response;
    }

}
