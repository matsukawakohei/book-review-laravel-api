<?php

namespace Tests\Feature\Controllers;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WishListControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ほしいものリストに追加_書籍登録あり(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);

        $param = [
            'isbn' => '978-86354-417-8',
        ];

        $url = route('api.v1.wishlist.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(201);
        
        $this->assertDatabaseHas('wish_lists',[
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function ほしいものリストに追加_書籍登録なし(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $book = Book::factory()->create([
            'isbn' => '978-86354-417-8'
        ]);

        $param = [
            'isbn' => '978-86354-417-8',
        ];

        $url = route('api.v1.wishlist.store');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(201);
        
        $this->assertDatabaseHas('wish_lists',[
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
