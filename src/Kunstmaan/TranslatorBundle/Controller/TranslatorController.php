<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TranslatorController extends Controller
{
    /**
     * @Route("/{domainId}", requirements={"domainId" = "\d+"}, name="KunstmaanTranslatorBundle_translations_show")
     * @Template()
     *
     * @return array
     */
    public function indexAction($domainId)
    {
        $this->container->get('kunstmaan_translator.repository.translation_domain')->findOneById(1);
    }
}