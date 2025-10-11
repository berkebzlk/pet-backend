<?php

namespace App\Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function getModel(): Model;
    public function getQuery(): \Illuminate\Database\Eloquent\Builder;
    public function create(array $data);
    public function findAll();
    public function findById(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function destroy(int $id);
}
