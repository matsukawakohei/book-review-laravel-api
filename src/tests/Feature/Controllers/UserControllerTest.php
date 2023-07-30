<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function ユーザー登録処理(): void
    {
        User::factory()->create(['email' => 'test@example.jp']);
        $param = [
            'name'                  => 'テスト',
            'email'                 => 'test2@example.jp',
            'password'              => 'asdf1234',
            'password_confirmation' => 'asdf1234',
        ];

        $url = route('api.v1.user.store');
        $this->postJson($url, $param)
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) => 
                $json->where('name', 'テスト')
                    ->where('email', 'test2@example.jp')
                    ->missing('password')
                    ->missing('access_token')
                    ->missing('password_reset_token')
                    ->missing('access_token_expire')
                    ->etc()
            );
        
        $this->assertDatabaseHas('users', [
            'name'     => $param['name'],
            'email'    => $param['email'],
        ]);
    }
}
