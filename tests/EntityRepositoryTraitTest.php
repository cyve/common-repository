<?php
namespace Cyve\EntityRepository\Test;

use Cyve\EntityRepository\EntityRepositoryTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class EntityRepositoryTraitTest extends TestCase
{
    public function testSave()
    {
        $entity = new \stdClass();

        $em = $this->createMock(EntityManager::class);
        $em->method('contains')->willReturn(false);
        $em->expects($this->once())->method('persist')->with($entity);
        $em->expects($this->once())->method('flush');

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getEntityManager']);
        $repository->method('getEntityManager')->willReturn($em);
        $repository->save($entity);
    }

    public function testSavePersisted()
    {
        $entity = new \stdClass();

        $em = $this->createMock(EntityManager::class);
        $em->method('contains')->willReturn(true);
        $em->expects($this->never())->method('persist')->with($entity);
        $em->expects($this->once())->method('flush');

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getEntityManager']);
        $repository->method('getEntityManager')->willReturn($em);
        $repository->save($entity);
    }

    public function testDelete()
    {
        $entity = new \stdClass();

        $em = $this->createMock(EntityManager::class);
        $em->method('contains')->willReturn(true);
        $em->expects($this->once())->method('remove')->with($entity);
        $em->expects($this->once())->method('flush');

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getEntityManager']);
        $repository->method('getEntityManager')->willReturn($em);
        $repository->delete($entity);
    }

    public function testDeleteNotPersisted()
    {
        $entity = new \stdClass();

        $em = $this->createMock(EntityManager::class);
        $em->method('contains')->willReturn(false);
        $em->expects($this->never())->method('remove');
        $em->expects($this->never())->method('flush');

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getEntityManager']);
        $repository->method('getEntityManager')->willReturn($em);
        $repository->delete($entity);
    }

    public function testPersist()
    {
        $entity = new \stdClass();

        $em = $this->createMock(EntityManager::class);
        $em->expects($this->once())->method('persist')->with($entity);

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getEntityManager']);
        $repository->method('getEntityManager')->willReturn($em);
        $repository->persist($entity);
    }

    public function testRemove()
    {
        $entity = new \stdClass();

        $em = $this->createMock(EntityManager::class);
        $em->expects($this->once())->method('remove')->with($entity);

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getEntityManager']);
        $repository->method('getEntityManager')->willReturn($em);
        $repository->remove($entity);
    }

    public function testRefresh()
    {
        $entity = new \stdClass();

        $em = $this->createMock(EntityManager::class);
        $em->expects($this->once())->method('refresh')->with($entity);

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getEntityManager']);
        $repository->method('getEntityManager')->willReturn($em);
        $repository->refresh($entity);
    }

    public function testFlush()
    {
        $em = $this->createMock(EntityManager::class);
        $em->expects($this->once())->method('flush');

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getEntityManager']);
        $repository->method('getEntityManager')->willReturn($em);
        $repository->flush();
    }

    /**
     * @dataProvider applyCriteriaDataProvider
     */
    public function testApplyCriteria($criteria, $expected)
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->method('hasField')->willReturnCallback(fn($property) => $property === 'foo');

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getClassMetadata', 'createQueryBuilder']);
        $repository->method('getClassMetadata')->willReturn($classMetadata);

        $reflectionMethod = new \ReflectionMethod($repository, 'applyCriteria');
        $reflectionMethod->setAccessible(true);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('andWhere')->with($expected);
        $qb->expects($this->once())->method('setParameter');

        $reflectionMethod->invokeArgs($repository, [$qb, $criteria]);
    }

    public function applyCriteriaDataProvider()
    {
        yield [['foo' => 'lorem ipsum', 'bar' => 'ipsum'], 'e.foo = :foo'];
        yield [['foo' => '%lorem'], 'e.foo LIKE :foo'];
        yield [['foo' => 'ipsum%'], 'e.foo LIKE :foo'];
        yield [['foo' => 'lorem%ipsum'], 'e.foo = :foo'];
    }

    public function testApplyOrderBy()
    {
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->method('hasField')->willReturnCallback(fn($property) => $property === 'foo');

        $repository = $this->getMockForTrait(EntityRepositoryTrait::class, [], '', true, true, true, ['getClassMetadata', 'createQueryBuilder']);
        $repository->method('getClassMetadata')->willReturn($classMetadata);

        $reflectionMethod = new \ReflectionMethod($repository, 'applyOrderBy');
        $reflectionMethod->setAccessible(true);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('addOrderBy')->with('e.foo', 'asc');

        $reflectionMethod->invokeArgs($repository, [$qb, ['foo' => 'asc', 'bar' => 'desc']]);
    }
}
