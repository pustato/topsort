<?php
declare(strict_types=1);

namespace Pustato\TopSort\Sortable;


use Pustato\TopSort\Contracts\Sortable;

class SortableObject implements Sortable
{
    /** @var mixed  */
    private $object;

    /** @var  string */
    private $id;

    /** @var array */
    private $dependencies;

    /**
     * SortableObject constructor.
     *
     * @param mixed $object
     * @param array|Sortable[] $dependencies
     */
    public function __construct(mixed $object, $dependencies = [])
    {
        $this->object = $object;
        $this->id = spl_object_hash($object);

        $this->dependencies = array_map(function($dependency) {
            if ($dependency instanceof Sortable) {
                return $dependency->getId();
            }

            return spl_object_hash($dependency);
        }, $dependencies);
    }

    /**
     * @return mixed
     */
    public function getObject(): mixed
    {
        return $this->object;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getDependenciesIds(): array
    {
        return $this->dependencies;
    }

}