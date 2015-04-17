<?php

namespace Kunstmaan\SeoBundle\Controller;

use Kunstmaan\SeoBundle\Entity\Robots;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RobotsController extends Controller
{
    /**
     * Generates the robots.txt content when available in the database and falls back to normal robots.txt if exists
     *
     * @Route(path="/robots.txt", name="KunstmaanSeoBundle_robots", defaults={"_format": "txt"})
     * @Template(template="@KunstmaanSeo/Admin/Robots/index.html.twig")
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $entity = $this->get('doctrine')->getRepository('KunstmaanSeoBundle:Robots')->findOneBy(array());
        $robots = $this->container->getParameter('robots_default');

        if ($entity and $entity->getRobotsTxt()) {
            $robots = $entity->getRobotsTxt();
        } else {
            $file = $request->getBasePath() . "robots.txt";
            if (file_exists($file)) {
                $robots = file_get_contents($file);
            }
        }

        return array('robots' => $robots);
    }
}