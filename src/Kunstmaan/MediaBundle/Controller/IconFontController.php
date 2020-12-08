<?php

namespace Kunstmaan\MediaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * IconFontController
 */
class IconFontController extends Controller
{
    /**
     * @Route("/chooser", name="KunstmaanMediaBundle_icon_font_chooser")
     * @Template("@KunstmaanMedia/IconFont/iconFontChooser.html.twig")
     *
     * @return array
     */
    public function iconFontChooserAction(Request $request)
    {
        $loader = $request->query->get('loader');
        $loaderData = json_decode($request->query->get('loader_data'), true);

        $iconFontManager = $this->get('kunstmaan_media.icon_font_manager');
        if (empty($loader)) {
            $loader = $iconFontManager->getDefaultLoader();
        } else {
            $loader = $iconFontManager->getLoader($loader);
        }
        $loader->setData($loaderData);

        return [
            'loader' => $loader,
        ];
    }
}
