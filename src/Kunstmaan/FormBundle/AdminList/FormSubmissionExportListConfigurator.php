<?php

namespace Kunstmaan\FormBundle\AdminList;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

/**
 * Exportlist configuration to list all the form submissions for a given NodeTranslation
 */
class FormSubmissionExportListConfigurator implements ExportListConfiguratorInterface
{

    /**
     * @var NodeTranslation
     */
    protected $nodeTranslation;
    protected $em;
    protected $exportFields;
    protected $iterator;
    protected $translator;

    /**
     * @param EntityManager   $em              The entity manager
     * @param NodeTranslation $nodeTranslation The node translation
     */
    public function __construct(EntityManager $em, $nodeTranslation, $translator)
    {
        $this->nodeTranslation = $nodeTranslation;
        $this->em = $em;
        $this->translator = $translator;
    }

    public function getExportFields()
    {
        return $this->exportFields;
    }

    public function addExportField($name, $header)
    {
        $this->exportFields[] = new Field($name, $header, false, null);

        return $this;
    }

    public function buildExportFields()
    {
        $this->addExportField('id', $this->translator->trans("Id"))
            ->addExportField('date', $this->translator->trans("Date"))
            ->addExportField('language', $this->translator->trans("Language"));
    }

    public function getIterator()
    {
        return $this->iterator;
    }

    public function buildIterator()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('fs')
            ->from('KunstmaanFormBundle:FormSubmission', 'fs')
            ->innerJoin('fs.node', 'n', 'WITH', 'fs.node = n.id')
            ->andWhere('n.id = :node')
            ->setParameter('node', $this->nodeTranslation->getNode()->getId())
            ->addOrderBy('fs.created', 'DESC');
        $iterableResult = $qb->getQuery()->iterate();
        $isHeaderWritten = false;

        $iterator = new ArrayCollection();
        foreach ($iterableResult as $row) {
            /* @var $submission FormSubmission */
            $submission = $row[0];

            // Write row data
            $data = array('id' => $submission->getId(), 'date' => $submission->getCreated()->format('d/m/Y H:i:s'), 'language' => $submission->getLang());
            foreach ($submission->getFields() as $field) {
                $header = mb_convert_encoding($this->translator->trans($field->getLabel()), 'ISO-8859-1', 'UTF-8');
                if (!$isHeaderWritten) {
                    $this->addExportField($header, $header);
                }
                $data[$header] = mb_convert_encoding($field->__toString(), 'ISO-8859-1', 'UTF-8');
            }
            $isHeaderWritten = true;
            $iterator->add(array($data));
        }

        $this->iterator = $iterator;
    }

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return string
     */
    public function getStringValue($item, $columnName)
    {
        return $item[$columnName];
    }
}
