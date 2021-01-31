<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_single_article()
    {
        $article = factory(Article::class)->create();

        $response = $this->jsonApi()->get(route('api.v1.articles.read', $article));

        $response->assertJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                    'created-at' => $article->created_at->toAtomString(),
                    'updated-at' => $article->updated_at->toAtomString(),
                ],
                'links' => [
                    'self' => route('api.v1.articles.read', $article)
                ]
            ]
        ]);

        $this->assertNull(
            $response->json('data.relationships.authors.data'),
            "The key 'data.relationship.authors.data' must be null."
        );
    }

    /** @test */
    public function can_fetch_all_articles()
    {
        $this->withoutExceptionHandling();

        $articles = factory(Article::class)->times(3)->create();

        $response = $this->jsonApi()->get(route('api.v1.articles.index'));

        $response->assertJson([
            'data' => [
                [
                    'type' => 'articles',
                    'id' => (string) $articles[0]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[0]->title,
                        'slug' => $articles[0]->slug,
                        'content' => $articles[0]->content,
                        'created-at' => $articles[0]->created_at->toAtomString(),
                        'updated-at' => $articles[0]->updated_at->toAtomString()
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.read', $articles[0])
                    ]
                ],
                [
                    'type' => 'articles',
                    'id' => (string) $articles[1]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[1]->title,
                        'slug' => $articles[1]->slug,
                        'content' => $articles[1]->content,
                        'created-at' => $articles[1]->created_at->toAtomString(),
                        'updated-at' => $articles[1]->updated_at->toAtomString()
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.read', $articles[1])
                    ]
                ],
                [
                    'type' => 'articles',
                    'id' => (string) $articles[2]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[2]->title,
                        'slug' => $articles[2]->slug,
                        'content' => $articles[2]->content,
                        'created-at' => $articles[2]->created_at->toAtomString(),
                        'updated-at' => $articles[2]->updated_at->toAtomString()
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.read', $articles[2])
                    ]
                ],
            ],
        ]);

    }
}
