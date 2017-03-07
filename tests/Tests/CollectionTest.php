<?php
declare(strict_types=1);

namespace Pustato\TopSort\Tests;

use PHPUnit\Framework\TestCase;
use Pustato\TopSort\Collection;
use Pustato\TopSort\Contracts\Sortable;
use Pustato\TopSort\Exceptions\CircularDependencyException;
use Pustato\TopSort\Exceptions\InvalidArgumentException;
use Pustato\TopSort\Exceptions\NodeNotFoundException;

class CollectionTest extends TestCase
{

    public function testConstructorWithAllInvalidArguments()
    {
        $this->expectException(InvalidArgumentException::class);
        new Collection([
            'a', 'b', 'c'
        ]);
    }

    public function testConstructorWithSeveralInvalidArguments()
    {
        $this->expectException(InvalidArgumentException::class);
        new Collection([
            $this->mockNode('a'), $this->mockNode('b'), 'c'
        ]);
    }

    public function testAddAndGet()
    {
        $collection = new Collection();

        $a = $this->mockNode('a', []);
        $b = $this->mockNode('b', []);

        $collection
            ->add($a)
            ->add($b)
        ;

        $this->assertEquals($a, $collection->get($a->getId()));
        $this->assertEquals($b, $collection->get($b->getId()));
    }

    public function testNotFoundExceptionOnEmptyCollection()
    {
        $collection = new Collection();

        $this->expectException(NodeNotFoundException::class);
        $collection->get('some string ID');
    }

    public function testNotFoundException()
    {
        $collection = new Collection();

        $a = $this->mockNode('a');
        $collection->add($a);

        $this->expectException(NodeNotFoundException::class);
        $collection->get('0');
    }

    public function testGetDictionary()
    {
        $a = $this->mockNode('a');
        $b = $this->mockNode('b');

        $collection = new Collection([$a, $b]);

        $dict = $collection->getDictionary();

        $this->assertArrayHasKey('a', $dict);
        $this->assertArrayHasKey('b', $dict);

        $this->assertEquals($dict['a'], $a);
        $this->assertEquals($dict['b'], $b);
    }

    public function testSort()
    {
        $a = $this->mockNode('a', ['b']);
        $b = $this->mockNode('b', []);

        $collection = new Collection([$a, $b]);
        $sorted = $collection->getSorted();

        $this->assertEquals($sorted[0], $b);
        $this->assertEquals($sorted[1], $a);
    }

    public function testSortCacheClearAfterAdd()
    {
        $a = $this->mockNode('a', ['b', 'c']);
        $b = $this->mockNode('b', []);
        $c = $this->mockNode('c', ['b']);

        $collection = new Collection([$b, $c]);
        $sorted = $collection->getSorted();

        $this->assertEquals(count($sorted), 2);
        $this->assertEquals($sorted[0], $b);
        $this->assertEquals($sorted[1], $c);

        $collection->add($a);
        $sorted = $collection->getSorted();

        $this->assertEquals(count($sorted), 3);
        $this->assertEquals($sorted[0], $b);
        $this->assertEquals($sorted[1], $c);
        $this->assertEquals($sorted[2], $a);
    }

    public function testNotFoundDependency()
    {
        $a = $this->mockNode('a', ['b']);
        $b = $this->mockNode('b', ['c']);

        $collection = new Collection([$a, $b]);
        $this->expectException(NodeNotFoundException::class);
        $collection->getSorted();
    }

    public function testCircularDependency()
    {
        $a = $this->mockNode('a', ['b']);
        $b = $this->mockNode('b', ['c']);
        $c = $this->mockNode('c', ['a']);

        $collection = new Collection([$a, $b, $c]);
        $this->expectException(CircularDependencyException::class);
        $collection->getSorted();

    }

    /**
     * Make node mock
     *
     * @param string $id
     * @param array $dependencies
     * @return mixed
     */
    private function mockNode(string $id, array $dependencies = []): Sortable
    {
        return \Mockery::mock(Sortable::class)
            ->shouldReceive('getId')
            ->andReturn($id)
            ->shouldReceive('getDependenciesIds')
            ->andReturn($dependencies)
            ->mock()
        ;
    }
}