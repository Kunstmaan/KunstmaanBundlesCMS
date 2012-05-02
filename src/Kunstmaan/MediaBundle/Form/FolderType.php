<?php

namespace Kunstmaan\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Kunstmaan\MediaBundle\Entity\File;

class FolderType extends AbstractType
{
    protected $entityname;
    public $gallery;

    public function __construct($name, $gallery = null)
    {
        $this->entityname = $name;
        $this->gallery = $gallery;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $gallery = $this->gallery;
        $type = $this;
        $builder
            ->add('name')
            ->add('parent', 'entity', array( 'class' => $this->getEntityName(), 'required' => false,
                              'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use($gallery, $type) {
                                  $qb = $er->createQueryBuilder('gallery');

                                  if($type->getEntityName()=="Kunstmaan\MediaBundle\Entity\Folder"){
                                  	$qb->where("gallery instance of 'Kunstmaan\MediaBundle\Entity\Folder'");
                                  }
                                  
                                  if($gallery != null){
                                      $ids = "gallery.id != ". $gallery->getId();
                                      $ids .= $type->addChildren($gallery);
                                      $qb->andwhere($ids);
                                    }
                       
                                    return $qb;
                              }
        ));
    }

    public function getName()
    {
        return 'kunstmaan_mediabundle_FolderType';
    }

    public function getEntityName()
    {
        return $this->entityname;
    }

    public function addChildren($gallery){
        $ids = "";
        foreach($gallery->getChildren() as $child){
            $ids .= " and gallery.id != " . $child->getId();
            $ids .= $this->addChildren($child);
        }
        return $ids;
    }
}