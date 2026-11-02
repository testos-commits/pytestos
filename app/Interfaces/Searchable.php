<?php
namespace App\Interfaces;

/**
 * Forces the class to implement the searchableTrait
 */
interface Searchable
{
    public function updatedSearchQuery(string $value): void;
}
