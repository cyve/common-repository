<?php

namespace Cyve\EntityRepository;

use Doctrine\ORM\QueryBuilder;

trait EntityRepositoryTrait
{
    /**
     * Persist and flush an object.
     */
    public function save($entity): void
    {
        $em = $this->getEntityManager();

        if (!$em->contains($entity)) {
            $em->persist($entity);
        }

        $em->flush();
    }

    /**
     * Remove and flush an object.
     */
    public function delete($entity): void
    {
        $em = $this->getEntityManager();

        if ($em->contains($entity)) {
            $em->remove($entity);
            $em->flush();
        }
    }

    /**
     * Persist an object.
     */
    public function persist($entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * Remove an object.
     */
    public function remove($entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * Refresh an object.
     */
    public function refresh($entity): void
    {
        $this->getEntityManager()->refresh($entity);
    }

    /**
     * Flush all objects.
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Iterate on all objects in the repository.
     */
    public function iterate(int $hydrationMode = 1): \Iterator
    {
        return $this->iterateBy([], null, null, null, $hydrationMode);
    }

    /**
     * Iterate on objects by a set of criteria.
     */
    public function iterateBy(array $criteria, array $orderBy = [], int $limit = null, $offset = null, $hydrationMode = 1)//: \Iterator
    {
        $builder = $this->createQueryBuilder('e');

        $this->applyCriteria($builder, $criteria);
        $this->applyOrderBy($builder, $orderBy);

        $builder->setMaxResults($limit);
        $builder->setFirstResult($offset);

        foreach ($builder->getQuery()->iterate(null, $hydrationMode) as $value) {
            yield $value[0];
        }
    }

    /**
     * Same as findBy() but allows "%" in criteria.
     */
    public function searchBy(array $criteria, array $orderBy = [], int $limit = null, $offset = null): array
    {
        $builder = $this->createQueryBuilder('e');

        $this->applyCriteria($builder, $criteria);
        $this->applyOrderBy($builder, $orderBy);

        $builder->setMaxResults($limit);
        $builder->setFirstResult($offset);

        return $builder->getQuery()->getResult();
    }

    /**
     * Same as count() but allows "%" in criteria.
     */
    public function countSearchBy(array $criteria): int
    {
        $builder = $this->createQueryBuilder('e');
        $builder->select('COUNT(e) AS count');

        $this->applyCriteria($builder, $criteria);

        return $builder->getQuery()->getSingleScalarResult();
    }

    private function applyCriteria(QueryBuilder $builder, array $criteria = []): void
    {
        $classMetadata = $this->getClassMetadata();

        foreach ($criteria as $property => $value) {
            if ($classMetadata->hasField($property)) {
                if ($value !== '' && (substr($value, 0, 1) === '%' || substr($value, -1) === '%')) {
                    $builder->andWhere(sprintf('e.%s LIKE :%s', $property, $property));
                } else {
                    $builder->andWhere(sprintf('e.%s = :%s', $property, $property));
                }

                $builder->setParameter($property, $value);
            }
        }
    }

    private function applyOrderBy(QueryBuilder $builder, array $orderBy = []): void
    {
        $classMetadata = $this->getClassMetadata();

        foreach($orderBy as $property => $value){
            if ($classMetadata->hasField($property) && in_array(strtolower($value), ['asc', 'desc'])) {
                $builder->addOrderBy(sprintf('e.%s', $property), $value);
            }
        }
    }
}
