<?php

namespace App\Repositories\Eloquent;

use App\Repositories\UserRepositoryInterface;
use App\Model\User;

class UserRepository implements UserRepositoryInterface{
    private $model;
    public function __construct(User $model){
        $this->model = $model;
    }
    public function getAll(){
        return $this->model->all();
    }
    public function getById($id){
        return $this->model->findById($id);
    }
    public function create(array $atributes){
        return $this->model->create($atributes);
    }
    public function update($id, array $atributes){
        $user = $this->model->findOrFail($id);
        $user->update($atributes);
        return $user;
    }
    public function delete($id){
        $this->model->delete($id);
    }
}
    



