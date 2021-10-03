<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\AdminBundle\Helper\VersionCheck\VersionChecker;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    /**
     * Index page for the settings
     *
     * @Route("/", name="KunstmaanAdminBundle_settings")
     * @Template("@KunstmaanAdmin/Settings/index.html.twig")
     *
     * @throws AccessDeniedException
     *
     * @return array
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return [];
    }

    /**
     * Show bundles version update information
     *
     * @Route("/bundle-version", name="KunstmaanAdminBundle_settings_bundle_version")
     * @Template("@KunstmaanAdmin/Settings/bundleVersion.html.twig")
     *
     * @throws AccessDeniedException
     *
     * @return array
     */
    public function bundleVersionAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if (!$this->versionChecker->isEnabled()) {
            return ['data' => null];
        }

        $data = null;
        try {
            $data = $this->versionChecker->check();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        return [
            'data' => $data,
        ];
    }
}
