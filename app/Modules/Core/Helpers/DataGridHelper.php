<?php

namespace App\Modules\Core\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DataGridHelper
{
    public Builder $query;
    private array $searchableFields = [];
    private array $sortableFields = [];
    private array $selectFields = [];
    private mixed $perPage = 10;
    private int $page = 1;
    private string $searchTerm = '';
    private array $sortBy = [];
    private array $filters = [];

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Set searchable fields
     */
    public function setSearchableFields(array $fields): self
    {
        $this->searchableFields = $fields;
        return $this;
    }

    /**
     * Set sortable fields
     */
    public function setSortableFields(array $fields): self
    {
        $this->sortableFields = $fields;
        return $this;
    }

    /**
     * Set fields to select
     */
    public function setSelectFields(array $fields): self
    {
        $this->selectFields = $fields;
        return $this;
    }

    /**
     * Set pagination
     */
    public function setPagination(mixed $perPage, int $page = 1): self
    {
        $this->perPage = $perPage;
        $this->page = $page;
        return $this;
    }

    /**
     * Set search term
     */
    public function setSearchTerm(string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;
        return $this;
    }

    /**
     * Set sorting
     */
    public function setSorting(array $sortBy): self
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    /**
     * Set filters
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Apply all transformations and return paginated results
     */
    public function getResults()
    {
        $this->applySelectFields();
        $this->applySearch();
        $this->applyFilters();
        $this->applySorting();

        if ($this->perPage === null) {
            $items = $this->query->get();
            return new LengthAwarePaginator(
                $items,
                $items->count(),
                max($items->count(), 1), // Prevent division by zero
                $this->page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );
        }
        return $this->query->paginate($this->perPage, ['*'], 'page', $this->page);
    }

    /**
     * Apply field selection
     */
    public function applySelectFields(): void
    {
        if (!empty($this->selectFields)) {
            $this->query->select($this->selectFields);
        }
    }

    /**
     * Apply search functionality
     */
    public function applySearch(): void
    {
        if (empty($this->searchTerm) || empty($this->searchableFields)) {
            return;
        }

        $this->query->where(function ($query) {
            foreach ($this->searchableFields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $this->searchTerm . '%');
            }
        });
    }

    /**
     * Apply filters
     */
    public function applyFilters(): void
    {
        foreach ($this->filters as $field => $value) {
            if (str_ends_with($field, '_not')) {
                $realField = substr($field, 0, -4);
                $this->query->where($realField, '!=', $value);
                continue;
            }

            if (is_array($value)) {
                // Range filter (e.g., ['min' => 10, 'max' => 100])
                if (isset($value['min'])) {
                    $this->query->where($field, '>=', $value['min']);
                }
                if (isset($value['max'])) {
                    $this->query->where($field, '<=', $value['max']);
                }
            } elseif (is_string($value) && !empty($value)) {
                $this->query->where($field, 'LIKE', '%' . $value . '%');
            } elseif (is_numeric($value)) {
                $this->query->where($field, $value);
            }
        }
    }

    /**
     * Apply sorting
     */
    public function applySorting(): void
    {
        foreach ($this->sortBy as $field => $direction) {
            if (in_array($field, $this->sortableFields) || empty($this->sortableFields)) {
                $this->query->orderBy($field, $direction);
            }
        }
    }

    /**
     * Get the query builder
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }


    /**
     * Create from Request
     */
    public static function fromRequest(Builder $query, Request $request): self
    {
        $dataGrid = new self($query);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $dataGrid->setPagination($perPage, $page);

        // Search
        $searchTerm = $request->get('search', '');
        $dataGrid->setSearchTerm($searchTerm);

        // Sorting
        $sortBy = $request->get('sort_by', []);
        if (is_string($sortBy)) {
            $sortBy = json_decode($sortBy, true) ?? [];
        }
        $dataGrid->setSorting($sortBy);

        // Filters
        $filters = $request->get('filters', []);
        if (is_string($filters)) {
            $filters = json_decode($filters, true) ?? [];
        }
        $dataGrid->setFilters($filters);

        // Fields to select
        $selectFields = $request->get('fields', []);
        if (is_string($selectFields)) {
            $selectFields = json_decode($selectFields, true) ?? [];
        }
        $dataGrid->setSelectFields($selectFields);

        return $dataGrid;
    }

    /**
     * Get available fields from model
     */
    public static function getAvailableFields(Model $model): array
    {
        return $model->getFillable();
    }

    /**
     * Get searchable fields from model
     */
    public static function getSearchableFields(Model $model): array
    {
        if (method_exists($model, 'getSearchableFields')) {
            return $model->getSearchableFields();
        }

        return $model->getFillable();
    }

    /**
     * Get sortable fields from model
     */
    public static function getSortableFields(Model $model): array
    {
        if (method_exists($model, 'getSortableFields')) {
            return $model->getSortableFields();
        }

        return $model->getFillable();
    }
}
