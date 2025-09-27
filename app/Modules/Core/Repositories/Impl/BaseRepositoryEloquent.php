<?php

namespace App\Modules\Core\Repositories\Impl;

use App\Modules\Core\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepositoryEloquent implements BaseRepositoryInterface
{
    public Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function findAll()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $this->model->update($data);
    }

    public function delete(int $id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function destroy(int $id)
    {
        return $this->model->where('id', $id)->forceDelete();
    }
}
