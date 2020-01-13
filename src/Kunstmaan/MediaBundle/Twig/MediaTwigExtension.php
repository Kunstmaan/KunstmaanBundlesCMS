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

    public function getCroppedVersion(CroppableMediaLink $croppableMediaLink)
    {
        $runTimeConfig = [
            'crop' => [
                'start' => [0, 0],
                'size' => [5000, 5000],
            ],
        ];
        if ($croppableMediaLink->getRunTimeConfig() !== null) {
            $runTimeConfig = unserialize($croppableMediaLink->getRunTimeConfig(), false);
        }

        return $this->manipulateImageService->manipulateOnTheFly($croppableMediaLink->getMedia(), $runTimeConfig);
    }
}
