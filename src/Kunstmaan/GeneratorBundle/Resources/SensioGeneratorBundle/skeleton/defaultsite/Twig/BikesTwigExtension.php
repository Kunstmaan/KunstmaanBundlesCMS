<?php

namespace {{ namespace }}\Twig;

use {{ namespace }}\Entity\Bike;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BikesTwigExtension extends AbstractExtension
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_bikes', [$this, 'getBikes']),
            new TwigFunction('get_submenu_items', [$this, 'getSubmenuItems']),
        ];
    }

    public function getBikes(): array
    {
        return $this->em->getRepository(Bike::class)->findAll();
    }

    public function getSubmenuItems(AbstractPage $page, string $locale): array
    {
        $items = [];

        $nv = $this->em->getRepository(NodeVersion::class)->getNodeVersionFor($page);
        if ($nv) {
            $nodeTranslations = $this->em->getRepository(NodeTranslation::class)->getOnlineChildren($nv->getNodeTranslation()->getNode(), $locale);
            foreach ($nodeTranslations as $nt) {
                $childPage = $nt->getPublicNodeVersion()->getRef($this->em);
                $items[] = ['nt' => $nt, 'page' => $childPage];
            }
        }

        return $items;
    }
}
