<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Repositories\Articles\ArticlesRepositoryInterface;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    private $articlesRepository;

    public function __construct(ArticlesRepositoryInterface $articlesRepository){
        $this->articlesRepository = $articlesRepository;
    }

    public function index()
    {
        $articles = $this->articlesRepository->getDataPagination();
        
        return $articles;
    }

    public function store(Request $request)
    {
        $articles = $this->articlesRepository->create($request);
        
        return $articles;
    }

    public function show($id)
    {
        $article = $this->articlesRepository->getById($id);
        
        return $article;
    }

    public function update(Request $request, $id)
    {
        $article = $this->articlesRepository->update($request, $id);

        return $article;
    }

    public function destroy($id)
    {
        $article = $this->articlesRepository->delete($id);

        return $article;
    }
}