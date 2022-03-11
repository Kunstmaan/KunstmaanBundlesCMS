<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

final class ModulesController
{
    /**
     * @Route("/", name="KunstmaanAdminBundle_modules")
     * @Template("@KunstmaanAdmin/Modules/index.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }
}
