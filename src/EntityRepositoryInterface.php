<?php

namespace Cyve\EntityRepository;

use Doctrine\Persistence\ObjectRepository;

interface EntityRepositoryInterface extends ObjectRepository
{
    public function save($entity): void;
    public function delete($entity): void;
    public function persist($entity): void;
    public function remove($entity): void;
    public function refresh($entity): void;
    public function flush(): void;
    public function iterate(int $hydrationMode = 1): \Iterator;
    public function iterateBy(array $criteria, array $orderBy =  [], int $limit = null, int $offset = null, int $hydrationMode = 1): \Iterator;
    public function searchBy(array $criteria, array $orderBy = [], int $limit = null, int $offset = null): array;
    public function countSearchBy(array $criteria): int;
}
