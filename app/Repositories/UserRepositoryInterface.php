<?php

namespace App\Repositories;

interface UserRepositoryInterface{
    public function getAll();
    public function getById($id);
    public function create(array $request);
    public function update($id, array $request);
    public function delete($id);
}