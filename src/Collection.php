<?php
declare(strict_types=1);

namespace Pustato\TopSort;


use Pustato\TopSort\Contracts\Sortable;
use Pustato\TopSort\Exceptions\CircularDependencyException;
use Pustato\TopSort\Exceptions\InvalidArgumentException;
use Pustato\TopSort\Exceptions\NodeNotFoundException;

class Collection
{
    /** @var array|Sortable[]  */
    private $nodesDictionary = [];

    /** @var array|Sortable[] */
    private $_sorted = null;

    /**
     * Collection constructor.
     *
     * @param array $nodes
     * @throws InvalidArgumentException
     */
    public function __construct(array $nodes = [])
    {
        foreach ($nodes as $node) {
            if ($node instanceof Sortable) {
                $this->nodesDictionary[$node->getId()] = $node;
            } else {
                throw new InvalidArgumentException(vsprintf('All nodes must implements "%s" interface.', [
                    Sortable::class
                ]));
            }
        }
    }

    /**
     * Add sortable node to collection
     *
     * @param Sortable $node
     * @return Collection
     */
    public function add(Sortable $node): Collection
    {
        $this->nodesDictionary[$node->getId()] = $node;
        $this->_sorted = null;
        return $this;
    }

    /**
     * Get node from collection by ID
     *
     * @param string $nodeId
     * @return Sortable
     * @throws NodeNotFoundException
     */
    public function get(string $nodeId): Sortable
    {
        if (!array_key_exists($nodeId, $this->nodesDictionary)) {
            throw new NodeNotFoundException(vsprintf('Node "%s" not found in collection', [
                $nodeId
            ]));
        }

        return $this->nodesDictionary[$nodeId];
    }

    /**
     * Get nodes as dictionary where key is node ID and values is node instances.
     *
     * @return array|Sortable[]
     */
    public function getDictionary(): array
    {
        return $this->nodesDictionary;
    }

    /**
     * @return array
     */
    public function getSorted(): array
    {
        if (!$this->_sorted) {
            $this->sort();
        }

        return $this->_sorted;
    }

    /**
     * Sort collection.
     *
     * @return Collection
     */
    protected function sort(): Collection
    {
        // Walk by nodesMap by recursive sort function
        $stack = [];
        $result = [];
        foreach ($this->nodesDictionary as $nodeId => $_) {
            $result = $this->visit($nodeId, $stack, $result);
        }

        // Resolve node IDs to Sortable objects
        $this->_sorted = array_map(function($nodeId) {
            return $this->get($nodeId);
        }, $result);

        return $this;
    }

    /**
     * Recursive function for sorting.
     *
     * @param string $nodeId visited node ID
     * @param array  $stack  stack of visits for detecting circular dependencies
     * @param array  $result intermediate result of sorting
     * @return array
     * @throws CircularDependencyException
     * @throws NodeNotFoundException
     */
    private function visit(string $nodeId, array $stack, array $result)
    {
        // Check is node already visited
        if (in_array($nodeId, $result)) {
            return $result;
        }

        // If node already in stack - we have circular dependency
        if (in_array($nodeId, $stack)) {
            throw new CircularDependencyException();
        }
        array_push($stack, $nodeId);

        foreach ($this->get($nodeId)->getDependenciesIds() as $dependencyId) {
            $result = $this->visit($dependencyId, $stack, $result);
        }

        array_pop($stack);
        array_push($result, $nodeId);
        return $result;
    }
}