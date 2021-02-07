<?php

namespace Tests\Unit\Commands;

use Tests\TestCase;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeneratePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_generate_permissions_to_registered_api_resources()
    {
        config([
            'json-api-v1.resources' => [
                'articles' => \App\Models\Article::class,
            ]
        ]);

        $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!');

        $this->assertDatabaseCount('permissions', count(Permission::$abilities));

        $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!');

        foreach(Permission::$abilities as $ability) {
            $this->assertDatabaseHas('permissions', [
                'name' => "articles:{$ability}"
            ]);
        }
    }
}
