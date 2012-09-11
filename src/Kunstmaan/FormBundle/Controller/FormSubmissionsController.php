<?php

namespace Kunstmaan\FormBundle\Controller;

use Ddeboer\DataImportBundle\Writer\CsvWriter;

use Kunstmaan\AdminBundle\Component\Security\Acl\Permission\PermissionMap;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
        $aclHelper = $this->container->get('kunstmaan.acl.helper');
        $formpagesadminlist = $this->get('adminlist.factory')->createList(new FormPageAdminListConfigurator(PermissionMap::PERMISSION_VIEW), $em);
        $formpagesadminlist->setAclHelper($aclHelper);
        $formpagesadminlist->bindRequest($request);

        return array('adminlist' => $formpagesadminlist);
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
        $aclHelper = $this->container->get('kunstmaan.acl.helper');
        $adminlist = $this->get("adminlist.factory")->createList(new FormSubmissionAdminListConfigurator($nodeTranslation), $em);
        $adminlist->setAclHelper($aclHelper);
        $adminlist->bindRequest($request);

        return array('nodetranslation' => $nodeTranslation, 'adminlist' => $adminlist);
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

        return array('nodetranslation' => $nodeTranslation, 'formsubmission' => $formSubmission,);
    }

    /**
     * Export as CSV
     * @param int $nodetranslationid
     *
     * @Route("/export/{nodetranslationid}", requirements={"nodetranslationid" = "\d+"}, name="KunstmaanFormBundle_formsubmissions_export")
     * @Method({"GET"})
     */
    public function exportAction($nodetranslationid)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->find($nodetranslationid);

        $tmpFilename = tempnam('/tmp', 'cb_csv_');
        $file = new \SplFileObject($tmpFilename);
        $writer = new CsvWriter($file);

        $qb = $em->createQueryBuilder()
                ->select('fs')
                ->from('KunstmaanFormBundle:FormSubmission', 'fs')
                ->innerJoin('fs.node', 'n', 'WITH', 'fs.node = n.id')
                ->andWhere('n.id = ?1')
                ->setParameter(1, $nodeTranslation->getNode()->getId())
                ->addOrderBy('fs.created', 'DESC');
        $iterableResult = $qb->getQuery()->iterate();
        $isHeaderWritten = false;
        $translator = $this->get('translator');

        foreach ($iterableResult AS $row) {
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
                $data[] = mb_convert_encoding($field->getValue(), 'ISO-8859-1', 'UTF-8');
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
