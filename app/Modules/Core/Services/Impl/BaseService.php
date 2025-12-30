<?php

namespace App\Modules\Core\Services\Impl;

use App\Modules\Core\Enums\HttpStatusEnum;
use App\Modules\Core\Services\BaseServiceInterface;
use App\Modules\Core\Repositories\BaseRepositoryInterface;
use App\Modules\Core\Helpers\DataGridHelper;
use App\Modules\Role\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Exception;

abstract class BaseService implements BaseServiceInterface
{
    public string $modelName;

    public function __construct(
        private readonly BaseRepositoryInterface $repository
    ) {
    }

    public function getModelName(): string
    {
        $model = $this->repository->getModel();
        // if the value is available from the translation file, return it
        $translationKey = 'models.' . get_class($model);
        $translated = __($translationKey);
        // if the translation is not found, $translated variable will be the same as the translation key
        // also check if the $translated variable is empty or null
        if (
            $translated !== $translationKey &&
            !empty($translated) &&
            is_string($translated)
        ) {
            return $translated;
        }

        // if $this->modelName is not empty, return it
        if (!empty($this->modelName)) {
            return $this->modelName;
        }

        // if $model variable is not empty, return it with class_basename
        if ($model) {
            return class_basename($model);
        }

        // if all are empty, return 'Record'
        return 'Record';
    }

    public function index(array $requestData = [], ?Builder $query = null)
    {
        $query = $query ?? $this->repository->getQuery();

        // get parameters from request data and validation
        $perPage = $requestData['perPage'] ?? null;
        $page = $requestData['page'] ?? 1;
        $searchTerm = $requestData['search'] ?? '';
        $sortBy = $requestData['sortBy'] ?? [];
        $filters = $requestData['filters'] ?? [];
        $selectFields = $requestData['fields'] ?? [];

        $page = is_numeric($page) && $page > 0 ? (int) $page : 1;
        $searchTerm = is_string($searchTerm) ? trim($searchTerm) : '';

        // decode JSON strings
        if (is_string($sortBy)) {
            $sortBy = json_decode($sortBy, true) ?? [];
        }
        if (is_string($filters)) {
            $filters = json_decode($filters, true) ?? [];
        }
        if (is_string($selectFields)) {
            $selectFields = json_decode($selectFields, true) ?? [];
        }

        // Array validation
        $sortBy = is_array($sortBy) ? $sortBy : [];
        $filters = is_array($filters) ? $filters : [];
        $selectFields = is_array($selectFields) ? $selectFields : [];

        // apply DataGrid features
        $dataGrid = new DataGridHelper($query);
        $dataGrid->setSearchableFields(DataGridHelper::getSearchableFields($this->repository->getModel()))
            ->setSortableFields(DataGridHelper::getSortableFields($this->repository->getModel()))
            ->setSelectFields($selectFields)
            ->setSearchTerm($searchTerm)
            ->setSorting($sortBy)
            ->setFilters($filters)
            ->setPagination($perPage, $page);

        // always return pagination
        return $dataGrid->getResults();
    }

    public function show(int $id)
    {
        $record = $this->repository->findById($id);
        if (!$record) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }
        return $record;
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->repository->findById($id);
        if (!$record) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        try {
            $record = $this->repository->findById($id);
            if (!$record) {
                throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
            }
            return $this->repository->delete($id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function destroy(int $id)
    {
        $record = $this->repository->findById($id);
        if (!$record) {
            throw new Exception(__('http.' . HttpStatusEnum::NOT_FOUND->value, ['attribute' => $this->getModelName()]), HttpStatusEnum::NOT_FOUND->value);
        }
        return $this->repository->destroy($id);
    }
}
