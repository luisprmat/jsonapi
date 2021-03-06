<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_update_articles()
    {
        $article = Article::factory()->create();

        $this->jsonApi()
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(401)
        ;
    }

    /** @test */
    public function authenticated_users_can_update_their_articles()
    {
        $article = Article::factory()->create();

        $category = Category::factory()->create();

        Sanctum::actingAs($user = $article->user, ['articles:update']);

        $this->jsonApi()
            ->withData([
                'type' => 'articles',
                'id' => $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Title changed',
                    'slug' => 'title-changed',
                    'content' => 'Content changed',
                ],
                'relationships' => [
                    'authors' => [
                        'data' => [
                            'id' => $user->getRouteKey(),
                            'type' => 'authors'
                        ]
                    ],
                    'categories' => [
                        'data' => [
                            'id' => $category->getRouteKey(),
                            'type' => 'categories'
                        ]
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
    public function authenticated_users_cannot_update_their_articles_without_permissions()
    {
        $article = Article::factory()->create();

        $category = Category::factory()->create();

        Sanctum::actingAs($user = $article->user);

        $this->jsonApi()
            ->withData([
                'type' => 'articles',
                'id' => $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Title changed',
                    'slug' => 'title-changed',
                    'content' => 'Content changed',
                ],
                'relationships' => [
                    'authors' => [
                        'data' => [
                            'id' => $user->getRouteKey(),
                            'type' => 'authors'
                        ]
                    ],
                    'categories' => [
                        'data' => [
                            'id' => $category->getRouteKey(),
                            'type' => 'categories'
                        ]
                    ]
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(403)
        ;

        $this->assertDatabaseMissing('articles', [
            'title' => 'Title changed',
            'slug' => 'title-changed',
            'content' => 'Content changed',
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_update_others_articles()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($user = User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type' => 'articles',
                'id' => $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Title changed',
                    'slug' => 'title-changed',
                    'content' => 'Content changed',
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(403)
        ;

        $this->assertDatabaseMissing('articles', [
            'title' => 'Title changed',
            'slug' => 'title-changed',
            'content' => 'Content changed',
        ]);
    }

    /** @test */
    public function can_update_the_title_only()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->user, ['articles:update']);

        $this->jsonApi()
            ->withData([
                'type' => 'articles',
                'id' => $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Title changed',
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
        $article = Article::factory()->create();

        Sanctum::actingAs($article->user, ['articles:update']);

        $this->jsonApi()
            ->withData([
                'type' => 'articles',
                'id' => $article->getRouteKey(),
                'attributes' => [
                    'slug' => 'slug-changed',
                ]
            ])
            ->patch(route('api.v1.articles.update', $article))
            ->assertStatus(200)
        ;

        $this->assertDatabaseHas('articles', [
            'slug' => 'slug-changed'
        ]);
    }

    /** @test */
    public function can_replace_the_categories()
    {
        $article = Article::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($article->user, ['articles:modify-categories']);

        $this->jsonApi()
            ->withData([
                'type' => 'categories',
                'id' => $category->getRouteKey()
            ])
            ->patch(route('api.v1.articles.relationships.categories.replace', $article))
            ->assertStatus(204)
        ;

        $this->assertDatabaseHas('articles', [
            'category_id' => $category->id
        ]);
    }

    /** @test */
    public function can_replace_the_authors()
    {
        $article = Article::factory()->create();
        $author = User::factory()->create();

        Sanctum::actingAs($article->user, ['articles:modify-authors']);

        $this->jsonApi()
            ->withData([
                'type' => 'authors',
                'id' => $author->getRouteKey()
            ])
            ->patch(route('api.v1.articles.relationships.authors.replace', $article))
            ->assertStatus(204)
        ;

        $this->assertDatabaseHas('articles', [
            'user_id' => $author->id
        ]);
    }
}
