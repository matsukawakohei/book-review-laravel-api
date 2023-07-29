<?php

namespace Tests\Feature\Requests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前のバリデーション_空の場合はNG(): void
    {
        $param = [
            'name' => '',
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'name' => [
                            trans('validation.required', ['attribute' => '名前'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function 名前のバリデーション_配列はNG(): void
    {
        $param = [
            'name' => ['aaa'] ,
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'name' => [
                            trans('validation.string', ['attribute' => '名前'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function 名前のバリデーション_51文字以上はNG(): void
    {
        $param = [
            'name' => str_repeat('あ', 51),
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'name' => [
                            trans('validation.max.string', ['attribute' => '名前', 'max' => 50])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function 名前のバリデーション_50文字はOK(): void
    {
        $param = [
            'name' => str_repeat('あ', 50),
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(fn (AssertableJson $json) => $json->missing('name')->etc());
    }

    /** @test */
    public function メールアドレスのバリデーション_空の場合はNG(): void
    {
        $param = [
            'email' => '',
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'email' => [
                            trans('validation.required', ['attribute' => 'メールアドレス'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function メールアドレスのバリデーション_配列はNG(): void
    {
        $param = [
            'email' => ['aaa'] ,
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'email' => [
                            trans('validation.string', ['attribute' => 'メールアドレス'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function メールアドレスのバリデーション_メールアドレスの形式NG(): void
    {
        $param = [
            'email' => 'aaaaa',
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'email' => [
                            trans('validation.email', ['attribute' => 'メールアドレス'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function メールアドレスのバリデーション_メールアドレスの重複NG(): void
    {
        User::factory()->create(['email' => 'test@example.jp']);
        $param = [
            'email' => 'test@example.jp',
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'email' => [
                            trans('validation.unique', ['attribute' => 'メールアドレス'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function メールアドレスのバリデーション_形式があっていて重複しないならOK(): void
    {
        User::factory()->create(['email' => 'test@example.jp']);
        $param = [
            'email' => 'test2@example.jp',
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(fn (AssertableJson $json) => $json->missing('email')->etc());
    }

    /** @test */
    public function パスワードのバリデーション_空の場合はNG(): void
    {
        $param = [
            'password' => '',
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'password' => [
                            trans('validation.required', ['attribute' => 'パスワード'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function パスワードのバリデーション_配列はNG(): void
    {
        $param = [
            'password' => ['aaa'] ,
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'password' => [
                            trans('validation.string', ['attribute' => 'パスワード'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function パスワードのバリデーション_8文字未満はNG(): void
    {
        $param = [
            'password' => str_repeat('a', 7),
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'password' => [
                            trans('validation.min.string', ['attribute' => 'パスワード', 'min' => 8])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function パスワードのバリデーション_半角英数字以外はNG(): void
    {
        $param = [
            'password' => str_repeat('あ', 8),
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'password' => [
                            trans('validation.alpha_num', ['attribute' => 'パスワード'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function パスワードのバリデーション_確認用と不一致はNG(): void
    {
        $param = [
            'password'              => str_repeat('a', 8),
            'password_confirmation' => str_repeat('b', 8),
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(
                [
                    'errors' => [
                        'password' => [
                            trans('validation.confirmed', ['attribute' => 'パスワード'])
                        ]
                    ]
                ]
        );
    }

    /** @test */
    public function パスワードのバリデーション_8文字以上で確認用と一致はOK(): void
    {
        $param = [
            'password'              => 'asdf1234',
            'password_confirmation' => 'asdf1234',
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(fn (AssertableJson $json) => $json->missing('password')->etc());
    }
}
