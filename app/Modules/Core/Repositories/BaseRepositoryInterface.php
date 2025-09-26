<?php

namespace App\Modules\Core\Repositories;

interface BaseRepositoryInterface
{
    public function create(array $data);
    public function findAll();
    public function findById(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function destroy(int $id);
}
