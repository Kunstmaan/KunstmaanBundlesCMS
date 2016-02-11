<?php

namespace Kunstmaan\MediaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * IconFontController
 */
class IconFontController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/chooser", name="KunstmaanMediaBundle_icon_font_chooser")
     * @Template()
     *
     * @return array
     */
    public function iconFontChooserAction(Request $request)
    {
        $loader = $request->query->get('loader');
        $loaderData = unserialize($request->query->get('loader_data'));

        $iconFontManager = $this->get('kunstmaan_media.icon_font_manager');
        if (empty($loader)) {
            $loader = $iconFontManager->getDefaultLoader();
        } else {
            $loader = $iconFontManager->getLoader($loader);
        }
        $loader->setData($loaderData);

        return array(
            'loader' => $loader
        );
    }
}
