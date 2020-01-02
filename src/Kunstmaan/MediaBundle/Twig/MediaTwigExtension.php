<?php

namespace Kunstmaan\MediaBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Kunstmaan\MediaBundle\Entity\ConfigurableMediaInterface;
use Kunstmaan\MediaBundle\Helper\ManipulateImageService;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
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

    public function getFilters()
    {
        return [
            new TwigFilter(
                'strip_letters',
                [$this, 'stripLetters']
            ),
        ];
    }

    public function getCroppedVersion($pagePart, $mediaGetter)
    {
        if ($pagePart instanceof ConfigurableMediaInterface) {
            $runTimeConfig = [
                'crop' => [
                    'start' => [0, 0],
                    'size' => [5000, 5000],
                ],
            ];
            if ($pagePart->getRunTimeConfig() !== null) {
                $runTimeConfig = unserialize($pagePart->getRunTimeConfig());
            }

            return $this->manipulateImageService->manipulateOnTheFly($pagePart->$mediaGetter(), $runTimeConfig);
        }

        throw new \Exception('PagePart not configured correctly to get manipulated version');
    }

    public function stripLetters($string) {
        return preg_replace('/[^0-9+]/', '', $string);
    }
}
