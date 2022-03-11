<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\Helper\IconFont\IconFontManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IconFontController extends AbstractController
{
    /** @var IconFontManager */
    private $iconFontManager;

    public function __construct(IconFontManager $iconFontManager)
    {
        $this->iconFontManager = $iconFontManager;
    }

    /**
     * @Route("/chooser", name="KunstmaanMediaBundle_icon_font_chooser")
     */
    public function iconFontChooserAction(Request $request): Response
    {
        $loader = $request->query->get('loader');
        $loaderData = json_decode($request->query->get('loader_data'), true);

        if (empty($loader)) {
            $loader = $this->iconFontManager->getDefaultLoader();
        } else {
            $loader = $this->iconFontManager->getLoader($loader);
        }
        $loader->setData($loaderData);

        return $this->render('@KunstmaanMedia/IconFont/iconFontChooser.html.twig', [
            'loader' => $loader,
        ]);
    }
}
