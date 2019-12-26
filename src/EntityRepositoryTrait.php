<?php

namespace Cyve\EntityRepository;

trait EntityRepositoryTrait
{
    /**
     * Persist and flush an object.
     *
     * @param object $entity
     * @param bool $flush
     */
    public function save($entity, bool $flush = true): void
    {
        $em = $this->getEntityManager();

        if (!$em->contains($entity)) {
            $em->persist($entity);
        }

        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Remove and flush an object.
     *
     * @param object $entity
     * @param bool $flush
     */
    public function remove($entity, bool $flush = true): void
    {
        $em = $this->getEntityManager();

        if ($em->contains($entity)) {
            $em->remove($entity);
        }

        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Refresh an object.
     *
     * @param object $entity
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
     * Add default value to argument $criteria.
     *
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }

    /**
     * Iterate on all objects in the repository.
     *
     * @param int $hydrationMode
     *
     * @return \Iterator
     */
    public function iterate(int $hydrationMode = 1): \Iterator
    {
        return $this->iterateBy([], null, null, null, $hydrationMode);
    }

    /**
     * Iterate on objects by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param int $hydrationMode
     *
     * @return \Iterator
     */
    public function iterateBy(array $criteria, array $orderBy = null, int $limit = null, $offset = null, $hydrationMode = 1): \Iterator
    {
        $builder = $this->createQueryBuilder('e');

        foreach ($criteria as $property => $value) {
            if ($this->_class->hasField($property)) {
                $builder->andWhere(sprintf('e.%s = :%s', $property, $property))->setParameter($property, $value);
            }
        }

        if ($orderBy) {
            foreach($orderBy as $property => $value){
                if ($this->_class->hasField($property)) {
                    $builder->addOrderBy(sprintf('e.%s', $property), $value);
                }
            }
        }

        $builder->setMaxResults($limit);
        $builder->setFirstResult($offset);

        foreach ($builder->getQuery()->iterate(null, $hydrationMode) as $value) {
            yield $value[0];
        }
    }
}
