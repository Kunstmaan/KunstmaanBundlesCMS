<?php

namespace Kunstmaan\MediaBundle\Form;

use Kunstmaan\MediaBundle\Entity\Folder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * FolderType
 */
class FolderType extends AbstractType
{
    /**
     * @var Folder
     */
    public $folder;

    /**
     * @param Folder $folder The folder
     */
    public function __construct(Folder $folder = null)
    {
        $this->folder = $folder;
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $folder = $this->folder;
        $type   = $this;
        $builder
            ->add('name')
            ->add(
                'rel',
                'choice',
                array(
                    'choices' => array(
                        'media'     => 'media',
                        'image'     => 'image',
                        'slideshow' => 'slideshow',
                        'video'     => 'video'
                    ),
                )
            )
            ->add(
                'parent',
                'entity',
                array(
                    'class'         => 'Kunstmaan\MediaBundle\Entity\Folder',
                    'required'      => true,
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $er) use ($folder, $type) {
                            $qb = $er->createQueryBuilder('folder');

                            if ($folder != null && $folder->getId() != null) {
                                $ids = "folder.id != " . $folder->getId();
                                $ids .= $type->addChildren($folder);
                                $qb->andwhere($ids);
                            }
                            $qb->andWhere('folder.deleted != true');

                            return $qb;
                        }
                )
            );
    }

    /**
     * @param Folder $folder
     *
     * @return string
     */
    public function addChildren(Folder $folder)
    {
        $ids = "";
        foreach ($folder->getChildren() as $child) {
            $ids .= " and folder.id != " . $child->getId();
            $ids .= $this->addChildren($child);
        }

        return $ids;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'kunstmaan_mediabundle_FolderType';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Kunstmaan\MediaBundle\Entity\Folder',
            )
        );
    }
}