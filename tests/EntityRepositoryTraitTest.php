<?php
namespace Cyve\EntityRepository\Test;

use Cyve\EntityRepository\EntityRepositoryTrait;
use PHPUnit\Framework\TestCase;

class EntityRepositoryTraitTest extends TestCase
{
    public function testEntityRepositoryTrait()
    {
        $repository = $this->getObjectForTrait(EntityRepositoryTrait::class);

        $this->assertTrue(method_exists($repository, 'save'));
        $this->assertTrue(method_exists($repository, 'remove'));
        $this->assertTrue(method_exists($repository, 'refresh'));
        $this->assertTrue(method_exists($repository, 'flush'));
        $this->assertTrue(method_exists($repository, 'count'));
        $this->assertTrue(method_exists($repository, 'searchBy'));
        $this->assertTrue(method_exists($repository, 'iterate'));
    }
}
