<?php

namespace Tests\Feature\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレスのバリデーション_空の場合はNG(): void
    {
        $param = [
            'email' => '',
        ];

        $url = route('api.v1.auth.login');
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

        $url = route('api.v1.auth.login');
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

        $url = route('api.v1.auth.login');
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
    public function メールアドレスのバリデーション_形式があっているならOK(): void
    {
        $param = [
            'email' => 'test@example.jp',
        ];

        $url = route('api.v1.auth.login');
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

        $url = route('api.v1.auth.login');
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

        $url = route('api.v1.auth.login');
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
    public function パスワードのバリデーション_文字列であればOK(): void
    {
        $param = [
            'password' => 'asdf1234',
        ];

        $url = route('api.v1.auth.login');
        $this->postJson($url, $param)
            ->assertStatus(400)
            ->assertJson(fn (AssertableJson $json) => $json->missing('password')->etc());
    }
}
