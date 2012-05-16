<?php
namespace Kunstmaan\FormBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Util\PropertyPath;

use Doctrine\ORM\NoResultException;

/**
 * Data transformation class
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class OneEntityToIdTransformer implements DataTransformerInterface
{
    private $em;
    private $class;
    private $property;
    private $queryBuilder;

    private $unitOfWork;

    public function __construct(EntityManager $em, $class, $property, $queryBuilder)
    {
        if (null !== $queryBuilder && ! $queryBuilder instanceof \Closure) {
            throw new UnexpectedTypeException($queryBuilder, 'Doctrine\ORM\QueryBuilder or \Closure');
        }

        if (null == $class) {
            throw new UnexpectedTypeException($class, 'string');
        }

        $this->em = $em;
        $this->unitOfWork = $this->em->getUnitOfWork();
        $this->class = $class;
        $this->queryBuilder = $queryBuilder;

        if ($property) {
            $this->property = $property;
        }
    }

    public function transform($data)
    {
        if (null === $data) {
            return null;
        }

        if (!$this->unitOfWork->isInIdentityMap($data)) {
            throw new FormException('Entities passed to the choice field must be managed');
        }

        if ($this->property) {
            $propertyPath = new PropertyPath($this->property);
            return $propertyPath->getValue($data);
        }

        return current($this->unitOfWork->getEntityIdentifier($data));
    }

    public function reverseTransform($data)
    {
        if (!$data) {
            return null;
        }

        $em = $this->em;
        $class = $this->class;
        $repository = $em->getRepository($class);

        if ($qb = $this->queryBuilder) {
            if ($qb instanceof \Closure) {
                $qb = $qb($repository, $data);
            }

            try {
                $result = $qb->getQuery()->getSingleResult();
            } catch (NoResultException $e) {
                $result = null;
            }
        } else {
            if ($this->property) {
                $result = $repository->findOneBy(array($this->property => $data));
            } else {
                $result = $repository->find($data);
            }
        }

        if (!$result) {
            throw new TransformationFailedException('Can not find entity');
        }

        return $result;
    }
}
