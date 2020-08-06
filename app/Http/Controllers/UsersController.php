<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    private $userRepo;
    public function __construct (UserRepositoryInterface $userRepo){
        $this->userRepo = $userRepo;
    }
    public function index(){
        $users = $this->userRepo->getAll();
        return $users;
    }
}
