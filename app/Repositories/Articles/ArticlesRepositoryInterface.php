<?php

namespace App\Repositories\Articles;

use Illuminate\Http\Request;

interface ArticlesRepositoryInterface{
    public function getDataPagination();
    public function getById($id);
    public function create(Request $request);
    public function update(Request $request, $id);
    public function delete($id);
}