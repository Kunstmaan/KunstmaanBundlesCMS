<?php

namespace Kunstmaan\NodeBundle\Helper;

use DateTime;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPaneCreator;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Symfony\Component\HttpFoundation\Request;

class FormDataAutoSaver implements AutoSaverInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /** @var TabPaneCreator */
    private $tabPaneCreator;

    public function __construct(
        EntityManager $em,
        TabPaneCreator $tabPaneCreator
    ) {
        $this->em = $em;
        $this->tabPaneCreator = $tabPaneCreator;
    }

    public function updateAutoSaveFromInput(HasNodeInterface $page, Request $request, Node $node, NodeTranslation $nodeTranslation, ?bool $isStructureNode, NodeVersion $nodeVersion): bool
    {
        $this->reverseFormParamsForAutoSave($page, $request);

        $tabPane = $this->tabPaneCreator->getDefaultTabPane(
            $request,
            $page,
            $node,
            $nodeTranslation,
            $isStructureNode,
            $nodeVersion
        );

        if ($tabPane->isValid()) {
            $nodeVersion->setUpdated(new DateTime());
            $this->em->persist($nodeTranslation);
            $this->em->persist($nodeVersion);
            $tabPane->persist($this->em);
            $this->em->flush();

            return true;
        }

        return false;
    }

    private function reverseFormParamsForAutoSave(HasNodeInterface $page, Request $request): void
    {
        $deletedIds = [];
        $deletedSequenceNumbers = [];
        $requestKeys = $request->request->keys();
        foreach ($requestKeys as $key) {
            $pos = strpos($key, '_deleted');
            if (false !== $pos) {
                $keyPart = substr($key, 0, $pos);
                $deletedIds[] = $keyPart;
                $request->request->remove($key);
            }
        }
        foreach ($deletedIds as $id) {
            $ref = $this->em->getRepository(PagePartRef::class)->find($id);
            if ($ref !== null) {
                $deletedSequenceNumbers[] = $ref->getSequencenumber();
            }
        }
        unset($deletedIds);
        unset($requestKeys);
        $pagePartRefs = $this->em->getRepository(PagePartRef::class)->getPagePartRefs($page);
        $pagePartRefsCopy = $pagePartRefs;
        /*** @var PagePartRef $ref */
        foreach ($pagePartRefsCopy as $key => $ref) {
            if (in_array($ref->getSequenceNumber(), $deletedSequenceNumbers, true)) {
                unset($pagePartRefs[$key]);
                $request->request->add([$ref->getId() . '_deleted' => true]);
                continue;
            }
        }
        unset($pagePartRefsCopy);
        unset($deletedSequenceNumbers);

        $mainSequence = $request->request->get('main_sequence');
        $sequenceCopy = $mainSequence;
        foreach ($sequenceCopy as $key => $sequence) {
            if (0 !== strpos($sequence, 'newpp_')) {
                $position = $this->em->getRepository(PagePartRef::class)->find($sequence)->getSequencenumber();
                /** @var PagePartRef $ref */
                foreach ($pagePartRefs as $ref) {
                    if ($ref->getSequencenumber() === $position) {
                        $mainSequence[$key] = $ref->getId();
                    }
                }
            }
        }
        unset($sequenceCopy);
        $mainSequence = array_values($mainSequence);
        $form = $request->request->get('form');
        $form['main']['id'] = $page->getId();
        $formCopy = $form;
        $count = 0;
        foreach (array_keys($formCopy) as $key) {
            if (0 === strpos($key, 'pagepartadmin_')) {
                $sequence = $mainSequence[$count];
                if (false === strpos($key, 'pagepartadmin_newpp_')) {
                    /** @var PagePartRef $ref */
                    foreach ($pagePartRefs as $ref) {
                        if ($ref->getId() === $sequence) {
                            $newKey = 'pagepartadmin_' . $ref->getId();
                            $form[$newKey] = $form[$key];
                            unset($form[$key]);
                        }
                    }
                }
                ++$count;
            }
        }
        unset($formCopy);
        unset($pagePartRefs);

        $request->request->set('main_sequence', $mainSequence);
        $request->request->set('form', $form);
    }
}
