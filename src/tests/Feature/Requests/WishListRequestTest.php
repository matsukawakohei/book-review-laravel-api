<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class WishListRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function isbnのバリデーション_空の場合はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        
        $param = [
            'isbn' => '',
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'isbn' => [
                            trans('validation.required', ['attribute' => 'isbn'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function isbnのバリデーション_配列はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn' => ['aaa'] ,
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'isbn' => [
                            trans('validation.string', ['attribute' => 'isbn'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function isbnのバリデーション_21文字以上はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn' => str_repeat('1', 21),
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'isbn' => [
                            trans('validation.max.string', ['attribute' => 'isbn', 'max' => 20])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function isbnのバリデーション_アルファベットはNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn' => str_repeat('a', 20),
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'isbn' => [
                            trans('validation.regex', ['attribute' => 'isbn'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function isbnのバリデーション_マルチバイト文字はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn' => str_repeat('あ', 20),
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'isbn' => [
                            trans('validation.regex', ['attribute' => 'isbn'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function isbnのバリデーション_20文字の数字とハイフンはOK(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn' => '978-86354-417-8',
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(fn (AssertableJson $json) => $json->missing('isbn')->etc());
    }
}
