<?php

namespace Kunstmaan\SeoBundle\Controller\Admin;

use Kunstmaan\SeoBundle\Entity\Robots;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RobotsController extends Controller
{
    /**
     * Generates the robots.txt content when available in the database and falls back to normal robots.txt if exists
     *
     * @Route(path="/robots.txt", name="KunstmaanSeoBundle_robots")
     * @Template(template="@KunstmaanSeo/Admin/Robots/index.html.twig")
     *
     */
    public function indexAction()
    {
        $repository = $this->get('doctrine')->getRepository('KunstmaanSeoBundle:Robots')->findOneBy(array());

        // Fall back to robots.txt if database is empty
        if(!$repository->getRobotsTxt()) {
            $robots =  "# Warning: No real robots.txt file has been found.
                        # Create a robots.txt file in your document root to modify this content

                        User-agent: *";
            $file = $this->get('request')->getBasePath() . "robots.txt";

            // fall back to default if file does not exists
            if(file_exists($file)) {
                $robots = file_get_contents($file);
            }

            return array('robots' => $robots);
        }

        return array('robots'  => $repository->getRobotsTxt());

    }
}