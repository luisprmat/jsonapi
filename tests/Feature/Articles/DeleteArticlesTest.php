<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_users_cannot_delete_articles()
    {
        $article = Article::factory()->create();

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
            ->assertStatus(401)
        ;
    }

    /** @test */
    public function authenticated_users_can_delete_their_articles()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->user, ['articles:delete']);

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
            ->assertStatus(204)
        ;
    }

    /** @test */
    public function authenticated_users_cannot_delete_their_articles_without_permissions()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->user);

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
            ->assertStatus(403)
        ;
    }

    /** @test */
    public function authenticated_users_cannot_delete_other_articles()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->delete(route('api.v1.articles.delete', $article))
            ->assertStatus(403)
        ;
    }
}
