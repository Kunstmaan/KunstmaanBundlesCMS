<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\NodeBundle\Helper\Menu\SimpleTreeView;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * WidgetsController
 */
class WidgetsController extends Controller
{
    /**
     * @Route("/ckselecturl", name="KunstmaanNodeBundle_ckselecturl")
     * @Template("KunstmaanNodeBundle:Widgets:selectLink.html.twig")
     *
     * @return array
     */
    public function ckSelectLinkAction()
    {
        $params = $this->getTemplateParameters();
        $params['cke'] = true;

        return $params;
    }

    /**
     * Select a link
     *
     * @Route   ("/selecturl", name="KunstmaanNodeBundle_selecturl")
     * @Template("KunstmaanNodeBundle:Widgets:selectLink.html.twig")
     *
     * @return array
     */
    public function selectLinkAction()
    {
        $params = $this->getTemplateParameters();
        $params['cke'] = false;

        return $params;
    }

    /**
     * Get the parameters needed in the template. This is common for the default link chooser and the cke link chooser.
     *
     * @return array
     */
    private function getTemplateParameters()
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $locale = $this->getRequest()->getLocale();

        $qb = $em->getConnection()->createQueryBuilder();
        $qb->select('n.id, n.parent_id, t.weight, t.title, t.online, t.url')
            ->from('kuma_nodes', 'n')
            ->leftJoin('n', 'kuma_node_translations', 't', "(t.node_id = n.id AND t.lang = ?)")
            ->where('n.deleted = 0')
            ->andWhere('t.online IN (0, 1)')
            ->addOrderBy('parent_id', 'ASC')
            ->addOrderBy('weight', 'ASC')
            ->addOrderBy('title', 'ASC');

        $permissionDef = new PermissionDefinition(array(PermissionMap::PERMISSION_VIEW));
        $permissionDef->setEntity('Kunstmaan\NodeBundle\Entity\Node');
        $permissionDef->setAlias('n');
        $qb = $this->get('kunstmaan_admin.acl.native.helper')->apply($qb, $permissionDef);

        $stmt = $em->getConnection()->prepare($qb->getSQL());
        $stmt->bindValue(1, $locale);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $simpleTreeView = new SimpleTreeView();
        foreach ($result as $data) {
            $simpleTreeView->addItem($data['parent_id'], $data);
        }

        // When the media bundle is available, we show a link in the header to the media chooser
        $allBundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists('KunstmaanMediaBundle', $allBundles)) {
            $params = array('linkChooser' => 1);
            $cKEditorFuncNum = $this->getRequest()->get('CKEditorFuncNum');
            if (!empty($cKEditorFuncNum)) {
                $params['CKEditorFuncNum'] = $cKEditorFuncNum;
            }
            $mediaChooserLink = $this->generateUrl('KunstmaanMediaBundle_chooser', $params);
        }

        return array(
            'tree' => $simpleTreeView,
            'mediaChooserLink' => $mediaChooserLink
        );
    }
}
