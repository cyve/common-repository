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
    public function save($entity, bool $flush = true): void
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
    public function remove($entity, bool $flush = true): void
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
    public function flush(): void
    {
        $this->_em->flush();
    }

    /**
     * Add default value to argument $criteria
     *
     * @inheritdoc
     */
    public function count(array $criteria = [])
    {
        return parent::count($criteria);
    }
}
