<?php

namespace Cyve\EntityRepository;

trait EntityRepositoryTrait
{
    /**
     * Persist an entity and flush EntityManager
     *
     * @param object $entity Doctrine entity to save
     * @param boolean $flush Flush if true
     *
     * @return void
     */
    public function save($entity, bool $flush = true)
    {
        if (!$this->_em->contains($entity)) {
            $this->_em->persist($entity);
        }

        if ($flush) {
            $this->_em->flush($entity);
        }
    }

    /**
     * Remove an entity and flush EntityManager
     *
     * @param object $entity Doctrine entity to remove
     * @param boolean $flush Flush if true
     *
     * @return void
     */
    public function remove($entity, bool $flush = true)
    {
        if ($this->_em->contains($entity)) {
            $this->_em->remove($entity);
        }

        if ($flush) {
            $this->_em->flush($entity);
        }
    }

    /**
     * Refresh an entity
     *
     * @param object $entity Doctrine entity to refresh
     *
     * @return object
     */
    public function refresh($entity)
    {
        $this->_em->refresh($entity);

        return $entity;
    }

    /**
     * Flush EntityManager
     *
     * @return void
     */
    public function flush()
    {
        $this->_em->flush();
    }

    /**
     * Add default value to argument $criteria
     *
     * @inheritdoc
     */
    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }

    /**
     * @param array $criteria Search criteria (['foo' => 'bar'] or ['foo' => ['value' => 'bar', 'strategy' => 'start']]). Available strategies: contain, start, end , equal, greater, greater_equal, less, less_equal.
     * @param array|null $orderBy
     * @param integer|null $limit
     * @param integer|null $offset
     * @return array
     */
    public function searchBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $builder = $this->createQueryBuilder('e');

        foreach ($criteria as $property => $value) {
            if (is_array($value) && isset($value['value'])) {
                $query = $value['value'];
                $strategy = $value['strategy'] ?? 'contain';
            } else {
                $query = $value;
                $strategy = 'contain';
            }

            switch ($strategy) {
                case 'equal':
                    $builder->andWhere('e.'.$property.' = :'.$property)->setParameter($property, $query);
                    break;
                case 'greater':
                    $builder->andWhere('e.'.$property.' > :'.$property)->setParameter($property, $query);
                    break;
                case 'greater_equal':
                    $builder->andWhere('e.'.$property.' >= :'.$property)->setParameter($property, $query);
                    break;
                case 'less':
                    $builder->andWhere('e.'.$property.' < :'.$property)->setParameter($property, $query);
                    break;
                case 'less_equal':
                    $builder->andWhere('e.'.$property.' <= :'.$property)->setParameter($property, $query);
                    break;
                case 'start':
                    $builder->andWhere('e.'.$property.' LIKE :'.$property)->setParameter($property, $query.'%');
                    break;
                case 'end':
                    $builder->andWhere('e.'.$property.' LIKE :'.$property)->setParameter($property, '%'.$query);
                    break;
                default:
                    $builder->andWhere('e.'.$property.' LIKE :'.$property)->setParameter($property, '%'.$query.'%');
            }
        }

        if ($orderBy) {
            foreach($orderBy as $property => $value){
                $builder->addOrderBy('e.'.$property, $value);
            }
        }
        $builder->setMaxResults($limit);
        $builder->setFirstResult($offset);

        return $builder->getQuery()->getResult();
    }

    /**
     * @param integer|null $offset
     * @return array
     */
    public function iterate($parameters = null, $hydrationMode = 1): \Iterator
    {
        return $this->createQueryBuilder('e')
            ->getQuery()
            ->iterate($parameters, $hydrationMode)
        ;
    }
}
