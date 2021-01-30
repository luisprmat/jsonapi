<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SortArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_sort_articles_by_title_asc()
    {
        factory(Article::class)->create(['title' => 'C Title']);
        factory(Article::class)->create(['title' => 'A Title']);
        factory(Article::class)->create(['title' => 'B Title']);

        $url = route('api.v1.articles.index', ['sort' => 'title']);

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'A Title',
            'B Title',
            'C Title',
        ]);
    }

    /** @test */
    public function it_can_sort_articles_by_title_desc()
    {
        factory(Article::class)->create(['title' => 'C Title']);
        factory(Article::class)->create(['title' => 'A Title']);
        factory(Article::class)->create(['title' => 'B Title']);

        $url = route('api.v1.articles.index', ['sort' => '-title']);

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'C Title',
            'B Title',
            'A Title',
        ]);
    }

    /** @test */
    public function it_can_sort_articles_by_title_and_content()
    {
        factory(Article::class)->create([
            'title' => 'C Title',
            'content' => 'B content'
        ]);

        factory(Article::class)->create([
            'title' => 'A Title',
            'content' => 'C content'
        ]);

        factory(Article::class)->create([
            'title' => 'B Title',
            'content' => 'D content'
        ]);

        // \DB::listen(function($db) {
        //     dump($db->sql);
        // }); // Show executed queries in sql

        $url = route('api.v1.articles.index') . '?sort=title,-content';

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'A Title',
            'B Title',
            'C Title',
        ]);

        $url = route('api.v1.articles.index') . '?sort=-content,title';

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'D content',
            'C content',
            'B content',
        ]);
    }

    /** @test */
    public function it_cannot_sort_articles_by_unknown_fields()
    {
        factory(Article::class)->times(3)->create();

        // \DB::listen(function($db) {
        //     dump($db->sql);
        // }); // Show executed queries in sql

        $url = route('api.v1.articles.index') . '?sort=unknown';

        $this->jsonApi()->get($url)->assertStatus(400);
    }
}
