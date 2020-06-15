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
        $query = Article::query();

        foreach(request('filter', []) as $filter => $value) {
            if($filter === 'year') {
                $query->whereYear('created_at', $value);
            } elseif($filter === 'month') {
                $query->whereMonth('created_at', $value);
            } else {
                $query->where($filter, 'LIKE', "%{$value}%");
            }
        }

        $articles = $query->applySorts()->jsonPaginate();
        // $articles = Article::applyFilters()->applySorts()->jsonPaginate(); //TODO: Mejorar ...

        return ArticleCollection::make($articles);
    }

    public function show(Article $article)
    {
        return ArticleResource::make($article);
    }
}
