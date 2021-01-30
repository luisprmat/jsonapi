<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_update_articles()
    {
        $article = factory(Article::class)->create();

        $this->jsonApi()
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(401)
        ;
    }

    /** @test */
    public function authenticated_users_can_update_articles()
    {
        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);

        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attributes' => [
                        'title' => 'Title changed',
                        'slug' => 'title-changed',
                        'content' => 'Content changed',
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200)
        ;

        $this->assertDatabaseHas('articles', [
            'title' => 'Title changed',
            'slug' => 'title-changed',
            'content' => 'Content changed',
        ]);
    }

    /** @test */
    public function can_update_the_title_only()
    {
        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);

        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attributes' => [
                        'title' => 'Title changed',
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200)
        ;

        $this->assertDatabaseHas('articles', [
            'title' => 'Title changed',
        ]);
    }

    /** @test */
    public function can_update_the_slug_only()
    {
        $article = factory(Article::class)->create();

        Sanctum::actingAs($article->user);

        $this->jsonApi()
            ->content([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                    'attributes' => [
                        'slug' => 'slug-changed',
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200)
        ;

        $this->assertDatabaseHas('articles', [
            'slug' => 'slug-changed'
        ]);
    }
}
