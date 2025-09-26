<?php

namespace App\Modules\User\Services;

interface UserServiceInterface
{
    public function index();
    public function show(int $id);
    public function store(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getCurrentUser(int $id);
    public function updateCurrentUser(int $id, array $data);
}
