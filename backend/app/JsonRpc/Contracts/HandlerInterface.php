<?php

namespace App\JsonRpc\Contracts;

interface HandlerInterface
{
    /**
     * Return the name of the module this handler supports, e.g. "Contact"
     */
    public function getModule(): string;

    /**
     * Create a new record
     */
    public function create(array $data, int $userId): array;

    /**
     * Update an existing record
     */
    public function update(int $id, array $data, int $userId): array;

    /**
     * Get a single record by ID
     */
    public function get(int $id, int $userId): array;

    /**
     * List paginated records with optional filters
     */
    public function list(array $filters, int $page, int $perPage, string $sortBy, string $sortOrder, int $userId): array;
}