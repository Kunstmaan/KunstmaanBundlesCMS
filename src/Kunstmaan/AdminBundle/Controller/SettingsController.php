<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
     * @Template()
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function indexAction()
    {
        $this->checkPermission('ROLE_ADMIN');

        return array();
    }

    /**
     * Show bundles version update information
     *
     * @Route("/bundle-version", name="KunstmaanAdminBundle_settings_bundle_version")
     * @Template("KunstmaanAdminBundle:Settings:bundleVersion.html.twig")
     *
     * @throws AccessDeniedException
     * @return array
     */
    public function bundleVersionAction()
    {
        $this->checkPermission();

        $versionChecker = $this->container->get('kunstmaan_admin.versionchecker');
        if (!$versionChecker->isEnabled()) {
            return array('data' => null);
        }

        $data = null;
        try {
            $data = $versionChecker->check();
        } catch (\Exception $e) {
            $this->container->get('logger')->error(
                $e->getMessage(),
                array('exception' => $e)
            );
        }

        return array(
            'data' => $data
        );
    }
}
