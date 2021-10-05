<?php

namespace Kunstmaan\SeoBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\SeoBundle\Entity\Robots;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class RobotsController
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var string */
    private $robotsDefault;

    public function __construct(EntityManagerInterface $em, string $robotsDefault)
    {
        $this->em = $em;
        $this->robotsDefault = $robotsDefault;
    }

    /**
     * Generates the robots.txt content when available in the database and falls back to normal robots.txt if exists
     *
     * @Route(path="/robots.txt", name="KunstmaanSeoBundle_robots", defaults={"_format": "txt"})
     * @Template("@KunstmaanSeo/Admin/Robots/index.html.twig")
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $entity = $this->em->getRepository(Robots::class)->findOneBy([]);
        $robots = $this->robotsDefault;

        if ($entity && $entity->getRobotsTxt()) {
            $robots = $entity->getRobotsTxt();
        } else {
            $file = $request->getBasePath() . 'robots.txt';
            if (file_exists($file)) {
                $robots = file_get_contents($file);
            }
        }

        return ['robots' => $robots];
    }
}
