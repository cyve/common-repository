<?php

namespace Cyve\EntityRepository;

use Doctrine\Common\Persistence\ObjectRepository;

interface EntityRepositoryInterface extends ObjectRepository
{
    public function save($entity, bool $flush = true): void;
    public function remove($entity, bool $flush = true): void;
    public function refresh($entity): void;
    public function flush(): void;
    public function count(array $criteria = []): int;
    public function iterate(int $hydrationMode = 1): \Iterator;
    public function iterateBy(array $criteria, array $orderBy = null, int $limit = null, int $offset = null, int $hydrationMode = 1): \Iterator;
}
