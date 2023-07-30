<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function ログイン処理_成功(): void
    {
        User::factory()->create([
            'name'     => 'テスト',
            'email'    => 'test@example.jp',
            'password' => 'password'
        ]);
        $param = [
            'email'    => 'test@example.jp',
            'password' => 'password',
        ];

        $url = route('api.v1.auth.login');
        $this->postJson($url, $param)
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json->where('name', 'テスト')
                    ->where('email', 'test@example.jp')
                    ->has('access_token')
                    ->missing('password')
                    ->missing('password_reset_token')
                    ->missing('access_token_expire')
                    ->etc()
            );
        
        $user = User::where('email', $param['email'])->first();
        $this->assertNotNull($user->access_token);
        $this->assertNotNull($user->access_token_expire);
        $this->assertSame(strlen($user->access_token), config('auth.token_size'));
    }

    /** @test */
    public function ログイン処理_メールアドレスが違う(): void
    {
        User::factory()->create([
            'name'     => 'テスト',
            'email'    => 'test@example.jp',
            'password' => 'password'
        ]);
        $param = [
            'email'    => 'test2@example.jp',
            'password' => 'password',
        ];

        $url = route('api.v1.auth.login');
        $this->postJson($url, $param)
            ->assertForbidden();
    }

    /** @test */
    public function ログイン処理_パスワードが違う(): void
    {
        User::factory()->create([
            'name'     => 'テスト',
            'email'    => 'test@example.jp',
            'password' => 'password'
        ]);
        $param = [
            'email'    => 'test@example.jp',
            'password' => 'passworddd',
        ];

        $url = route('api.v1.auth.login');
        $this->postJson($url, $param)
            ->assertForbidden();
    }
}
