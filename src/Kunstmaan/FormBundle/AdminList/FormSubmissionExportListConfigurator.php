<?php

namespace Kunstmaan\FormBundle\AdminList;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\ExportListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Export list configuration to list all the form submissions for a given NodeTranslation
 */
class FormSubmissionExportListConfigurator implements ExportListConfiguratorInterface
{
    /**
     * @var NodeTranslation
     */
    protected $nodeTranslation;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var array
     */
    protected $exportFields;

    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var int
     */
    protected $batchSize;

    /**
     * @param int $batchSize
     */
    public function __construct(EntityManagerInterface $em, NodeTranslation $nodeTranslation, TranslatorInterface $translator, $batchSize = 20)
    {
        $this->nodeTranslation = $nodeTranslation;
        $this->em = $em;
        $this->translator = $translator;
        $this->batchSize = $batchSize;
    }

    /**
     * Build the filters, default none
     */
    public function buildFilters()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        return $this->exportFields;
    }

    /**
     * @param string $name
     * @param string $header
     *
     * @return FormSubmissionExportListConfigurator
     */
    public function addExportField($name, $header)
    {
        $this->exportFields[] = new Field($name, $header, false, null);

        return $this;
    }

    /**
     * Build export fields
     */
    public function buildExportFields()
    {
        $this->addExportField('id', $this->translator->trans('Id'))
            ->addExportField('date', $this->translator->trans('Date'))
            ->addExportField('language', $this->translator->trans('Language'));
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * Build iterator
     *
     * NOTE : The submission fields are added as export fields as well ...
     */
    public function buildIterator()
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('fs')
            ->from('KunstmaanFormBundle:FormSubmission', 'fs')
            ->innerJoin('fs.node', 'n', 'WITH', 'fs.node = n.id')
            ->andWhere('n.id = :node')
            // only export the requested language, bc headers aren't translated correctly
            ->andWhere('fs.lang = :lang')
            ->setParameter('node', $this->nodeTranslation->getNode()->getId())
            ->setParameter('lang', $this->nodeTranslation->getLang())
            ->addOrderBy('fs.created', 'DESC');
        $iterableResult = $qb->getQuery()->iterate();
        $isHeaderWritten = false;

        $collection = new ArrayCollection();
        $i = 0;
        foreach ($iterableResult as $row) {
            /* @var FormSubmission $submission */
            $submission = $row[0];

            // Write row data
            $data = [
                'id' => $submission->getId(),
                'date' => $submission->getCreated()->format('d/m/Y H:i:s'),
                'language' => $submission->getLang(),
            ];
            foreach ($submission->getFields() as $field) {
                $fieldName = preg_replace('/\d+/', '', $field->getFieldName()) . '' . $field->getSequence();
                if (!$isHeaderWritten) {
                    $this->addExportField($fieldName, $this->translator->trans($field->getLabel()));
                }
                $data[$fieldName] = (string) $field;
            }
            $isHeaderWritten = true;
            $collection->add([$data]);

            if (($i % $this->batchSize) === 0) {
                $this->em->clear();
            }
            ++$i;
        }

        $this->iterator = $collection->getIterator();
    }

    /**
     * @param array  $item       The item
     * @param string $columnName The column name
     *
     * @return string
     */
    public function getStringValue($item, $columnName)
    {
        if (array_key_exists($columnName, $item)) {
            return $item[$columnName];
        }

        return '';
    }
}
