<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\AdminBundle\Form\EditUserType;
use Kunstmaan\AdminBundle\Form\EditGroupType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Form\UserType;
use Kunstmaan\AdminBundle\Form\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\AdminBundle\AdminList\UserAdminListConfigurator;
use Kunstmaan\AdminBundle\AdminList\GroupAdminListConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SettingsController extends Controller
{
	/**
	 * @Route("/", name="KunstmaanAdminBundle_settings")
	 * @Template()
	 */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$request = $this->getRequest();
    	$adminlist = $this->get("adminlist.factory")->createList(new UserAdminListConfigurator(), $em);
    	$adminlist->bindRequest($request);
    	
    	return array(
    			'useradminlist' => $adminlist
    	);
    }

    /**
     * @Route("/users", name="KunstmaanAdminBundle_settings_users")
     * @Template()
     */
    public function usersAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $adminlist = $this->get("adminlist.factory")->createList(new UserAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'useradminlist' => $adminlist,
            'addparams'     => array()
        );
    }
    
    /**
     * @Route("/users/add", name="KunstmaanAdminBundle_settings_users_add")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function adduserAction() {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$request = $this->getRequest();
    	$helper = new User();
    	$form = $this->createForm(new UserType($this->container), $helper);
    	
    	if ('POST' == $request->getMethod()) {
    		$form->bindRequest($request);
    		if ($form->isValid()){
    				$em->persist($helper);
    				$em->flush();
    			return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
    		}
    	}

    	return array(
    			'form' => $form->createView(),
    	);
    }
    
    /**
     * @Route("/users/{user_id}/edit", requirements={"user_id" = "\d+"}, name="KunstmaanAdminBundle_settings_users_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function edituserAction($user_id) {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$request = $this->getRequest();
    	$helper = $em->getRepository('KunstmaanAdminBundle:User')->getUser($user_id, $em);
    	$form = $this->createForm(new EditUserType($this->container), $helper);
    	
    	if ('POST' == $request->getMethod()) {
    		$form->bindRequest($request);
    		if ($form->isValid()){
    			$em->persist($helper);
    			$em->flush();
    			return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_users'));
    		}
    	}
    
    	return array(
    			'form' => $form->createView(),
    			'user' => $helper
    	);
    }

    /**
     * @Route("/groups", name="KunstmaanAdminBundle_settings_groups")
     * @Template()
     */
    public function groupsAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $adminlist = $this->get("adminlist.factory")->createList(new GroupAdminListConfigurator(), $em);
        $adminlist->bindRequest($request);

        return array(
            'groupadminlist' => $adminlist,
            'addparams'     => array()
        );
    }

    /**
     * @Route("/groups/add", name="KunstmaanAdminBundle_settings_groups_add")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function addgroupAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $request = $this->getRequest();
        $helper = new Group();
        $form = $this->createForm(new GroupType($this->container), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em->persist($helper);
                $em->flush();
                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/groups/{group_id}/edit", requirements={"group_id" = "\d+"}, name="KunstmaanAdminBundle_settings_groups_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editgroupAction($group_id) {
        $em = $this->getDoctrine()->getEntityManager();

        $request = $this->getRequest();
        $helper = $em->getRepository('KunstmaanAdminBundle:Group')->find($group_id);
        $form = $this->createForm(new EditGroupType($this->container), $helper);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em->persist($helper);
                $em->flush();
                return new RedirectResponse($this->generateUrl('KunstmaanAdminBundle_settings_groups'));
            }
        }

        return array(
            'form' => $form->createView(),
            'group' => $helper
        );
    }
}
