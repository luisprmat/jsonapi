<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_articles_by_title()
    {
        Article::factory()->create([
            'title' => 'Aprende Laravel desde Cero'
        ]);

        Article::factory()->create([
            'title' => 'Otro título'
        ]);

        $url = route('api.v1.articles.index', ['filter[title]' => 'Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel desde Cero')
            ->assertDontSee('Otro título')
        ;
    }

    /** @test */
    public function can_filter_articles_by_content()
    {
        Article::factory()->create([
            'content' => '<div>Aprende Laravel desde Cero</div>'
        ]);

        Article::factory()->create([
            'content' => '<div>Otro título</div>'
        ]);

        $url = route('api.v1.articles.index', ['filter[content]' => 'Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel desde Cero')
            ->assertDontSee('Otro título')
        ;
    }

    /** @test */
    public function can_filter_articles_by_year()
    {
        Article::factory()->create([
            'title' => 'Article from 2020',
            'created_at' => now()->year(2020)
        ]);

        Article::factory()->create([
            'title' => 'Article from 2021',
            'created_at' => now()->year(2021)
        ]);

        $url = route('api.v1.articles.index', ['filter[year]' => 2020]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from 2020')
            ->assertDontSee('Article from 2021')
        ;
    }

    /** @test */
    public function can_filter_articles_by_month()
    {
        Article::factory()->create([
            'title' => 'Article from March',
            'created_at' => now()->month(3)
        ]);

        Article::factory()->create([
            'title' => 'Another Article from March',
            'created_at' => now()->month(3)
        ]);

        Article::factory()->create([
            'title' => 'Article from January',
            'created_at' => now()->month(1)
        ]);

        // This test fails if i run it today (2021-01-29) "now()->month(2)" returns 2021-03-01
        // $url = route('api.v1.articles.index', ['filter[month]' => 2]);

        $url = route('api.v1.articles.index', ['filter[month]' => 3]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from March')
            ->assertSee('Another Article from March')
            ->assertDontSee('Article from January')
        ;
    }

    /** @test */
    public function cannot_filter_articles_by_unknown_filters()
    {
        Article::factory()->create();

        $url = route('api.v1.articles.index', ['filter[unknown]' => 2]);

        $this->jsonApi()->get($url)->assertStatus(400);
    }

    /** @test */
    public function can_search_articles_by_title_and_content()
    {
        Article::factory()->create([
            'title' => 'Article from Aprendible',
            'content' => 'Content'
        ]);

        Article::factory()->create([
            'title' => 'Another Article',
            'content' => 'Content Aprendible ...'
        ]);

        Article::factory()->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);

        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from Aprendible')
            ->assertSee('Another Article')
            ->assertDontSee('Title 2')
        ;
    }

    /** @test */
    public function can_search_articles_by_title_and_content_with_multiple_terms()
    {
        Article::factory()->create([
            'title' => 'Article from Aprendible',
            'content' => 'Content'
        ]);

        Article::factory()->create([
            'title' => 'Another Article',
            'content' => 'Content Aprendible ...'
        ]);

        Article::factory()->create([
            'title' => 'Another Laravel Article',
            'content' => 'Content...'
        ]);

        Article::factory()->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);

        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(3, 'data')
            ->assertSee('Article from Aprendible')
            ->assertSee('Another Article')
            ->assertSee('Another Laravel Article')
            ->assertDontSee('Title 2')
        ;
    }

    /** @test */
    function can_filter_articles_by_category()
    {
        Article::factory()->count(2)->create();

        $category = Category::factory()->hasArticles(2)->create();

        $this->jsonApi()
            ->filter(['categories' => $category->getRouteKey()])
            ->get(route('api.v1.articles.index'))
            ->assertJsonCount(2, 'data')
        ;
    }

    /** @test */
    function can_filter_articles_by_multiple_categories()
    {
        Article::factory()->count(2)->create();

        $category1 = Category::factory()->hasArticles(2)->create();
        $category2 = Category::factory()->hasArticles(3)->create();

        $this->jsonApi()
            ->filter([
                'categories' => $category1->getRouteKey().','.$category2->getRouteKey()
            ])
            ->get(route('api.v1.articles.index'))
            ->assertJsonCount(5, 'data')
        ;
    }

    /** @test */
    function can_filter_articles_by_authors()
    {
        $author = User::factory()->hasArticles(2)->create();
        Article::factory()->count(2)->create();

        $this->jsonApi()
            ->filter(['authors' => $author->name])
            ->get(route('api.v1.articles.index'))
            ->assertJsonCount(2, 'data')
        ;
    }

    /** @test */
    function can_filter_articles_by_multiple_authors()
    {
        $author1 = User::factory()->hasArticles(2)->create();
        $author2 = User::factory()->hasArticles(3)->create();

        Article::factory()->count(2)->create();

        $this->jsonApi()
            ->filter([
                'authors' => $author1->name.','.$author2->name
            ])
            ->get(route('api.v1.articles.index'))
            ->assertJsonCount(5, 'data')
        ;
    }
}
