<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResourceObject;
use App\Http\Resources\ResourceCollection;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::applyFilters()->applySorts()->jsonPaginate();

        return ResourceCollection::make($articles);
    }

    public function show(Article $article)
    {
        return ResourceObject::make($article);
    }
}
