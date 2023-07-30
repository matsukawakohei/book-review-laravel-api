<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HasAccessTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function アクセストークン・ユーザーIDなし(): void
    {
        $param = [
            'isbn'    => '978-86354-417-8',
            'comment' => str_repeat('あ', 200),
            'point'   => 4.5,
        ];

        $url = route('api.v1.review.store');
        $this->postJson($url, $param)
            ->assertForbidden();
    }

    /** @test */
    public function アクセストークンなし(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn'    => '978-86354-417-8',
            'comment' => str_repeat('あ', 200),
            'point'   => 4.5,
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertForbidden();
    }

    /** @test */
    public function ユーザーIDなし(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn'    => '978-86354-417-8',
            'comment' => str_repeat('あ', 200),
            'point'   => 4.5,
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
        ])->postJson($url, $param)
            ->assertForbidden();
    }

    /** @test */
    public function アクセストークンとユーザーIDが不一致(): void
    {
        $user1 = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $user2 = User::factory()->create([
            'access_token'        => str_repeat('b', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn'    => '978-86354-417-8',
            'comment' => str_repeat('あ', 200),
            'point'   => 4.5,
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user2->id,
        ])->postJson($url, $param)
            ->assertForbidden();
    }

    /** @test */
    public function アクセストークン有効期限切れ(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->subMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn'    => '978-86354-417-8',
            'comment' => str_repeat('あ', 200),
            'point'   => 4.5,
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertForbidden();
    }
}
