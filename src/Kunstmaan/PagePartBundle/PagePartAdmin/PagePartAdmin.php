<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 23:22
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

use Symfony\Component\Form\FormBuilder;

class PagePartAdmin {

    protected $request = null;

    protected $configurator = null;

    protected $em = null;

    protected $page = null;

    protected $context = null;

    protected $pageparts = array();

    protected $container = null;

    function __construct(AbstractPagePartAdminConfigurator $configurator, $em, $page, $context, $container)
    {
        $this->configurator = $configurator;
        $this->em = $em;
        $this->page = $page;
        $this->context = $context;
        $this->container = $container;
    }

    /**
     * @param $request
     */
    public function preBindRequest($request){
        $this->request = $request;
        $pagepartrefs = $this->getPagePartRefs();
        { //remove pageparts
            foreach($pagepartrefs as &$pagepartref){
                if("true" == $this->request->request->get($pagepartref->getId()."_deleted")){
                    $pagepart = $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());
                    $this->em->remove($pagepart);
                    $this->em->remove($pagepartref);
                }
            }
        }
        $addpagepart = $this->request->request->get("addpagepart_".$this->getContext());
        if(is_string($addpagepart) && $addpagepart != ''){
        	$addpagepartposition = $this->request->request->get($this->getContext()."_addposition");
        	if(!is_string($addpagepartposition) || $addpagepartposition == ''){
        		$addpagepartposition = sizeof($this->getPagePartRefs())+1;
        	}
            $newpagepart = new $addpagepart;
            $this->em->persist($newpagepart);
            $this->em->flush();
            $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($this->page, $newpagepart, $addpagepartposition);
        }
        //$this->em->flush();
    }

    public function bindRequest($request){
        $this->request = $request;
        { //re-order pageparts
            $sequences = $this->request->request->get($this->context."_".$this->page->getId()."_".get_class($this->page)."_sequence");
            for($i = 0; $i < sizeof($sequences); $i++) {
                $sequence = $sequences[$i];
                $pagepartref = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->find($sequence);
                if(is_object($pagepartref)){
                    $pagepartref->setSequencenumber($i+1);
                    //$this->em->persist($pagepartref);
                }
            }
        }
        //$this->em->flush();
    }

    public function getContext(){
        return $this->context;
    }

    public function getPossiblePagePartTypes(){
        return $this->container->get("kunstmaan_admin.pageparts_builder")->getPossiblePagePartTypes();
    }

    public function getPagePartRefs(){
        $queryBuilder = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->createQueryBuilder('b');
        //set page and pageentityname
        $query = $queryBuilder
        ->where('b.pageId = :pageId and b.pageEntityname = :pageEntityname and b.context = :context')
        ->setParameter('pageId', $this->page->getId())
        ->setParameter('pageEntityname', get_class($this->page))
        ->setParameter('context', $this->context)
        ->orderBy("b.sequencenumber")->getQuery();
        return $query->getResult();
    }

    public function getPagePart(\Kunstmaan\PagePartBundle\Entity\PagePartRef $pagepartref){
        $result = $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());
        return $result;
    }

    public function getType($pagepart){
        $possiblePagePartTypes = $this->getPossiblePagePartTypes();
        foreach( $possiblePagePartTypes as &$pageparttype){
            if($pageparttype['class'] == get_class($pagepart)){
                return $pageparttype['name'];
            }
        }
        return "no name";
    }

    public function adaptForm(FormBuilder $formbuilder, $formfactory, array $options = array()){
        $pagepartrefs = $this->getPagePartRefs();
        $data = $formbuilder->getData();
        for($i = 0; $i < sizeof($pagepartrefs); $i++) {
            $pagepartref = $pagepartrefs[$i];
            $pagepart = $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());
            $pageparts[] = $pagepart;
            $data['pagepartadmin'.$pagepartref->getId()] = $pagepart;
            $formbuilder->add('pagepartadmin'.$pagepartref->getId(), $pagepart->getDefaultAdminType());
        }
        $formbuilder->setData($data);
    }
}
