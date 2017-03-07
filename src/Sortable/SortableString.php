<?php
declare(strict_types=1);

namespace Pustato\TopSort\Sortable;


use Pustato\TopSort\Contracts\Sortable;

class SortableString implements Sortable
{
    /** @var string */
    private $value;

    /** @var array */
    private $dependenciesIds = [];

    /**
     * SortableString constructor.
     *
     * @param string $value
     * @param array|Sortable[] $dependencies
     */
    public function __construct(string $value, array $dependencies = [])
    {
        $this->value = $value;
        $this->dependenciesIds = array_map(function($dependency) {
            if ($dependency instanceof Sortable) {
                return $dependency->getId();
            }

            return $dependency;
        }, $dependencies);
    }

    /**
     * Get item value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function getDependenciesIds(): array
    {
        return $this->dependenciesIds;
    }


}