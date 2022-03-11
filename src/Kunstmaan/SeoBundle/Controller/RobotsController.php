<?php

namespace Kunstmaan\SeoBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\SeoBundle\Entity\Robots;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RobotsController extends AbstractController
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
     */
    public function indexAction(Request $request): Response
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

        return $this->render('@KunstmaanSeo/Admin/Robots/index.html.twig', ['robots' => $robots]);
    }
}
