<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SettingsController extends AbstractController
{
    /** @var VersionChecker */
    private $versionChecker;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(VersionChecker $versionChecker, LoggerInterface $logger)
    {
        $this->versionChecker = $versionChecker;
        $this->logger = $logger;
    }

    #[Route(path: '/', name: 'KunstmaanAdminBundle_settings')]
    public function indexAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('@KunstmaanAdmin/Settings/index.html.twig');
    }

    #[Route(path: '/bundle-version', name: 'KunstmaanAdminBundle_settings_bundle_version')]
    public function bundleVersionAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if (!$this->versionChecker->isEnabled()) {
            return $this->render('@KunstmaanAdmin/Settings/bundleVersion.html.twig', ['data' => null]);
        }

        $data = null;
        try {
            $data = $this->versionChecker->check();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        return $this->render('@KunstmaanAdmin/Settings/bundleVersion.html.twig', [
            'data' => $data,
        ]);
    }
}
