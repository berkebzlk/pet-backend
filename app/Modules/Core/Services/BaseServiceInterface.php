<?php

namespace App\Modules\Core\Services;

interface BaseServiceInterface
{
    /**
     * Get all records
     */
    public function index();

    /**
     * Get single record by ID
     */
    public function show(int $id);

    /**
     * Create new record
     */
    public function store(array $data);

    /**
     * Update existing record
     */
    public function update(int $id, array $data);

    /**
     * Soft delete record
     */
    public function delete(int $id);

    /**
     * Force delete record
     */
    public function destroy(int $id);
}
