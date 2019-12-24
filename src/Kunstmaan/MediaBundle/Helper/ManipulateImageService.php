<?php

namespace Kunstmaan\MediaBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Kunstmaan\MediaBundle\Entity\ConfigurableMediaInterface;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ManipulateImageService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Slugifier */
    private $slugifier;

    /** @var FilterService */
    private $filterService;

    public function __construct(EntityManagerInterface $em, Slugifier $slugifier, FilterService $filterService)
    {
        $this->slugifier = $slugifier;
        $this->filterService = $filterService;
        $this->em = $em;
    }

    public function manipulateOnTheFly(Media $media, array $runTimeConfig, int $pagePartRefId = null): string
    {
        $filename = $media->getOriginalFilename();
        $filename = str_replace(array('/', '\\', '%'), '', $filename);

        $parts = pathinfo($filename);
        $filename = $this->slugifier->slugify($parts['filename']);
        if (\array_key_exists('extension', $parts)) {
            $filename .= '.'.strtolower($parts['extension']);
        }

        $path = sprintf(
            'uploads/media/%s/%s',
            $media->getUuid(),
            $filename
        );

        if($pagePartRefId !== null) {
            /** @var PagePartRef $pagePartRef */
            $pagePartRef = $this->em->getRepository(PagePartRef::class)->find($pagePartRefId);
            $pagePart = $this->em->getRepository($pagePartRef->getPagePartEntityname())->find($pagePartRef->getPagePartId());
            if($pagePart instanceof ConfigurableMediaInterface) {
                $pagePart->setRunTimeConfig(serialize($runTimeConfig));
                $this->em->flush();
            }
        }

        $url = $this->filterService->getUrlOfFilteredImageWithRuntimeFilters($path, 'optim', $runTimeConfig);

        return $url;
    }
}
