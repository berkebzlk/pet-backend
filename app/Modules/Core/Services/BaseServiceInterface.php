<?php

namespace App\Modules\Core\Services;

use Illuminate\Database\Eloquent\Builder;

interface BaseServiceInterface
{
    public function getModelName(): string;

    public function index(array $requestData = [], ?Builder $query = null);

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
