<?php

namespace Kunstmaan\MediaBundle\Controller;

use Kunstmaan\MediaBundle\Helper\IconFont\IconFontManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class IconFontController
{
    /** @var IconFontManager */
    private $iconFontManager;

    public function __construct(IconFontManager $iconFontManager)
    {
        $this->iconFontManager = $iconFontManager;
    }

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

        if (empty($loader)) {
            $loader = $this->iconFontManager->getDefaultLoader();
        } else {
            $loader = $this->iconFontManager->getLoader($loader);
        }
        $loader->setData($loaderData);

        return [
            'loader' => $loader,
        ];
    }
}
