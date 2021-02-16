<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Main settings controller
 */
class SettingsController extends BaseSettingsController
{
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

        $versionChecker = $this->container->get('kunstmaan_admin.versionchecker');
        if (!$versionChecker->isEnabled()) {
            return ['data' => null];
        }

        $data = null;

        try {
            $data = $versionChecker->check();
        } catch (\Exception $e) {
            $this->container->get('logger')->error(
                $e->getMessage(),
                ['exception' => $e]
            );
        }

        return [
            'data' => $data,
        ];
    }
}
