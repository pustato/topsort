<?php
declare(strict_types=1);

namespace Pustato\TopSort\Contracts;


interface Sortable
{
    /**
     * Return unique item id
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Return array of dependency ids
     *
     * @return array
     */
    public function getDependenciesIds(): array;
}