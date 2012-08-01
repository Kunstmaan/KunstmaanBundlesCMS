<?php

namespace Kunstmaan\MediaPagePartBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;

class DownloadPagePartAdminType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('media', 'media', array('pattern' => 'KunstmaanMediaBundle_chooser_filechooser'));
        /*$builder->add('media', 'entity', array(
            'required'  => false,
            'class'     => 'KunstmaanMediaBundle:Media',
            'property'  => 'url',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('b')->where('b.classtype = ?1')->setParameter(1, 'File');
            },
            //'attr' => array('class' => 'hidden')
        ));*/
    }

    public function getName() {
        return 'kunstmaan_mediabundle_downloadpageparttype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\MediaPagePartBundle\Entity\DownloadPagePart',
        );
    }

}
