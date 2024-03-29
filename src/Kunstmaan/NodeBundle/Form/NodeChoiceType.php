<?php

namespace Kunstmaan\NodeBundle\Form;

use Kunstmaan\NodeBundle\Repository\NodeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeChoiceType extends AbstractType
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'page_class' => [],
                'locale' => null,
                'online' => true,
                'class' => 'Kunstmaan\NodeBundle\Entity\Node',
                'choice_label' => 'nodeTranslations[0].title',
                'query_builder' => function (NodeRepository $er) {
                    return $er->createQueryBuilder('n');
                },
            ]
        );

        $queryBuilderNormalizer = function (Options $options, $queryBuilder) {
            if (\is_callable($queryBuilder)) {
                $queryBuilder = \call_user_func($queryBuilder, $options['em']->getRepository($options['class']));
            }

            if (!empty($options['page_class'])) {
                $queryBuilder
                    ->select('n, nt')
                    ->innerJoin('n.nodeTranslations', 'nt')
                    ->innerJoin('nt.publicNodeVersion', 'nv')
                    ->andWhere('nt.online = :online')
                    ->andWhere('nt.lang = :lang')
                    ->andWhere('n.deleted = :deleted')
                    ->andWhere('n.refEntityName IN(:refEntityName)')
                    ->setParameter('lang', $options['locale'] ? $options['locale'] : $this->getCurrentLocale())
                    ->setParameter('refEntityName', $options['page_class'])
                    ->setParameter('online', $options['online'])
                    ->setParameter('deleted', false);
            }

            return $queryBuilder;
        };

        $resolver->setNormalizer('query_builder', $queryBuilderNormalizer);
        $resolver->setAllowedTypes('query_builder', ['null', 'callable', 'Doctrine\ORM\QueryBuilder']);
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return EntityType::class;
    }

    private function getCurrentLocale()
    {
        if ($this->requestStack->getCurrentRequest() === null) {
            return null;
        }

        return $this->requestStack->getCurrentRequest()->getLocale();
    }
}
