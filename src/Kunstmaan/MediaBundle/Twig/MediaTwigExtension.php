<?php

namespace Kunstmaan\MediaBundle\Twig;

use Kunstmaan\MediaBundle\Entity\CroppableMediaLink;
use Kunstmaan\MediaBundle\Helper\ManipulateImageService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaTwigExtension extends AbstractExtension
{
    /** @var ManipulateImageService */
    private $manipulateImageService;

    public function __construct(
        ManipulateImageService $manipulateImageService
    )
    {
        $this->manipulateImageService = $manipulateImageService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'get_cropped_version',
                [$this, 'getCroppedVersion']
            ),
        ];
    }

    public function getCroppedVersion(CroppableMediaLink $croppableMediaLink, string $view = '')
    {
        return $this->manipulateImageService->manipulateOnTheFly($croppableMediaLink, $view);
    }
}
