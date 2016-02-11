<?php

namespace Kunstmaan\FixturesBundle\Builder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\FixturesBundle\Loader\Fixture;
use Kunstmaan\FixturesBundle\Populator\Populator;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;

class PagePartBuilder implements BuilderInterface
{
    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var \Kunstmaan\PagePartBundle\Repository\PagePartRefRepository */
    private $pagePartRepo;

    /** @var \Kunstmaan\FixturesBundle\Populator\Populator */
    private $populator;

    public function __construct(EntityManager $em, Populator $populator)
    {
        $this->em           = $em;
        $this->pagePartRepo = $em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $this->populator    = $populator;
    }

    public function canBuild(Fixture $fixture)
    {
        if ($fixture->getEntity() instanceof PagePartInterface) {
            return true;
        }

        return false;
    }

    public function preBuild(Fixture $fixture)
    {
        return;
    }

    public function postBuild(Fixture $fixture)
    {
        return;
    }

    public function postFlushBuild(Fixture $fixture)
    {
        $params       = $fixture->getParameters();
        $translations = $fixture->getTranslations();
        $original     = $fixture->getEntity();

        if (!isset($params['page']) || empty($translations)) {
            throw new \Exception(
                'No page reference and/or translations detected for pagepart fixture ' . $fixture->getName() . ' (' . $fixture->getClass() . ')'
            );
        }

        $pageFixture = $params['page'];
        if (!$pageFixture instanceof Fixture) {
            throw new \Exception(
                'Could not find a reference "' . $params['page'] . '"" for fixture ' . $fixture->getName() . ' (' . $fixture->getClass() . ')'
            );
        }

        $additionalEntities = $pageFixture->getAdditionalEntities();
        $pp                 = $original;
        $first              = null;
        foreach ($translations as $language => $data) {
            $key = $pageFixture->getName() . '_' . $language;
            if (!isset($additionalEntities[$key])) {
                continue;
            }

            if ($first !== null) {
                $pp = clone $original;
            }

            $page = $additionalEntities[$key];
            $this->populator->populate($pp, $data);
            $this->em->persist($pp);
            $this->em->flush($pp);

            // Find latest position.
            $position = array_key_exists('position', $params) ? $params['position'] : null;
            $context  = isset($params['context']) ? $params['context'] : 'main';
            if (is_null($position)) {
                $pageParts = $this->pagePartRepo->getPagePartRefs($page, $context);
                $position  = count($pageParts) + 1;
            }

            $this->pagePartRepo->addPagePart($page, $pp, $position, $context);
            $first = false;
        }
    }
}
