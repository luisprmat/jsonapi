<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::applyFilters()->applySorts()->jsonPaginate();

        return ArticleCollection::make($articles);
    }

    public function show(Article $article)
    {
        return ArticleResource::make($article);
    }
}
