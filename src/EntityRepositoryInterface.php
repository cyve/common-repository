<?php

namespace Cyve\EntityRepository;

interface EntityRepositoryInterface
{
    public function find($id);
    public function findAll();
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
    public function findOneBy(array $criteria);
    public function getClassName();
    public function save($entity, bool $flush = true);
    public function remove($entity, bool $flush = true);
    public function flush();
    public function count(array $criteria = []);
    public function searchBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
    public function iterate($parameters = null, $hydrationMode = 1);
}
