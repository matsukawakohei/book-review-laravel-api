<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReviewRequestTest extends TestCase
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

    /** @test */
    public function コメントのバリデーション_空の場合はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        
        $param = [
            'comment' => '',
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
                        'comment' => [
                            trans('validation.required', ['attribute' => '書評'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function コメントのバリデーション_配列はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'comment' => ['aaa'] ,
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
                        'comment' => [
                            trans('validation.string', ['attribute' => '書評'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function コメントのバリデーション_文字列ならOK(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'comment' => str_repeat('あ', 100),
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(fn (AssertableJson $json) => $json->missing('comment')->etc());
    }

    /** @test */
    public function 点数のバリデーション_空の場合はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        
        $param = [
            'point' => '',
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
                        'point' => [
                            trans('validation.required', ['attribute' => '点数'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function 点数のバリデーション_5より大きい数値はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        
        $param = [
            'point' => 5.1,
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
                        'point' => [
                            trans('validation.between.numeric', ['attribute' => '点数','min' => 0, 'max' => 5])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function 点数のバリデーション_0より小さい数値はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        
        $param = [
            'point' => -0.1,
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
                        'point' => [
                            trans('validation.between.numeric', ['attribute' => '点数','min' => 0, 'max' => 5])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function 点数のバリデーション_小数点以下2桁以上はNG(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        
        $param = [
            'point' => 4.99,
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
                        'point' => [
                            trans('validation.decimal', ['attribute' => '点数', 'decimal' => 1])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function 点数のバリデーション_0以上5以下の小数点以下1桁ならOK(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'point' => 4.9,
        ];

        $url = route('api.v1.review.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(fn (AssertableJson $json) => $json->missing('point')->etc());
    }
}
