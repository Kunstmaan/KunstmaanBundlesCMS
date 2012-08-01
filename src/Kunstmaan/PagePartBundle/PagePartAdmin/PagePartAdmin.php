<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

use Symfony\Component\Form\FormBuilderInterface;

use Kunstmaan\AdminBundle\Modules\ClassLookup;

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
        if($context){
            $this->context = $context;
        } else if($this->configurator->getDefaultContext()){
            $this->context = $this->configurator->getDefaultContext();
        } else {
            $this->context = "main";
        }
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
            $this->em->flush();
        }

    }

    public function postBindRequest($request)
    {
    	$addpagepart = $this->request->request->get("addpagepart_".$this->getContext());
    	if(is_string($addpagepart) && $addpagepart != ''){
    		$addpagepartposition = $this->request->request->get($this->getContext()."_addposition");
    		if(!is_string($addpagepartposition) || $addpagepartposition == ''){
    			$addpagepartposition = sizeof($pagepartrefs)+1;
    		}
    		$newpagepart = new $addpagepart;
    		$this->em->persist($newpagepart);
    		$this->em->flush();
    		$pagepartref = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($this->page, $newpagepart, $addpagepartposition, $this->context);
    	}
    }

    public function bindRequest($request){
        $this->request = $request;
        { //re-order pageparts
            $sequences = $this->request->request->get($this->context."_".$this->page->getId()."_".ClassLookup::getClass($this->page)."_sequence");
            for($i = 0; $i < sizeof($sequences); $i++) {
                $sequence = $sequences[$i];
                $pagepartref = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->find($sequence);
                if(is_object($pagepartref)){
                    $pagepartref->setSequencenumber($i+1);
                    $this->em->persist($pagepartref);
                }
            }
        }
    }

    public function getContext(){
        return $this->context;
    }

	/**
	 * This getter returns an array holding info on page part types that can be added to the page.
	 * The types are filtererd here, based on the amount of page parts of a certain type that can be added to the page.
	 *
	 * @return mixed
	 */
    public function getPossiblePagePartTypes()
	{
		$possiblePPTypes = $this->configurator->getPossiblePagePartTypes();
		$em = $this->container->get('doctrine')->getEntityManager();

		// filter page part types that can only be added x times to the page.
		// to achieve this, provide a 'pagelimit' parameter when adding the pp type in your PagePartAdminConfiguration
		if(!empty($possiblePPTypes))
		{
			for ($i=0; $i<sizeof($possiblePPTypes); $i++)
			{
				$possibleTypeArray = $possiblePPTypes[$i];
				if(array_key_exists('pagelimit', $possibleTypeArray))
				{
					$pageLimit = $possibleTypeArray['pagelimit'];
					$formPPCount = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')
						->countPagePartsOfType($this->page, $possibleTypeArray['class'], $this->configurator->getDefaultContext());

					if($formPPCount >= $pageLimit) {
						// 'pagelimit' reached -> remove pp type
						unset($possiblePPTypes[$i]);
					}
				}
			}
		}

        return $possiblePPTypes;
    }

    public function getName(){
        return $this->configurator->getName();
    }

    public function getPagePartRefs(){
        $queryBuilder = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef')->createQueryBuilder('b');
        //set page and pageentityname
        $query = $queryBuilder
        ->where('b.pageId = :pageId and b.pageEntityname = :pageEntityname and b.context = :context')
        ->setParameter('pageId', $this->page->getId())
        ->setParameter('pageEntityname', ClassLookup::getClass($this->page))
        ->setParameter('context', $this->context)
        ->orderBy("b.sequencenumber")->getQuery();

        return $query->getResult();
    }

    public function getPagePart(\Kunstmaan\PagePartBundle\Entity\PagePartRef $pagepartref){
        $result = $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());

        return $result;
    }

    public function getType($pagepart){
        $possiblePagePartTypes = $this->configurator->getPossiblePagePartTypes();
        foreach( $possiblePagePartTypes as &$pageparttype){
            if($pageparttype['class'] == ClassLookup::getClass($pagepart)){
                return $pageparttype['name'];
            }
        }

        return "no name";
    }

    public function adaptForm(FormBuilderInterface $formbuilder, $formfactory, array $options = array()){
        $pagepartrefs = $this->getPagePartRefs();
        if(sizeof($pagepartrefs) > 0) {
        	$ppformbuilder = $formbuilder->getFormFactory()->createNamedBuilder('form', 'pagepartadmin_'.$this->getContext());
        	$data = $formbuilder->getData();
        	for($i = 0; $i < sizeof($pagepartrefs); $i++) {
        		$pagepartref = $pagepartrefs[$i];
        		$pagepart = $this->em->getRepository($pagepartref->getPagePartEntityname())->find($pagepartref->getPagePartId());
        		$pageparts[] = $pagepart;
        		$data['pagepartadmin_'.$this->getContext()]['pagepartadmin_'.$this->getContext().'_'.$pagepartref->getId()] = $pagepart;
        		$ppformbuilder->add('pagepartadmin_'.$this->getContext().'_'.$pagepartref->getId(), $pagepart->getDefaultAdminType());
        	}
        	$formbuilder->setData($data);
        	$formbuilder->add($ppformbuilder);
        }
    }
}
