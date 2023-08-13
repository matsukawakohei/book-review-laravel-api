<?php

namespace Tests\Feature\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\WishList;
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

    /** @test */
    public function ほしいものリスト一覧(): void
    {
        $user1 = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $user2 = User::factory()->create([
            'access_token'        => str_repeat('b', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $wishLists = WishList::factory(10)->create([
            'user_id' => $user1->id
        ]);
        WishList::factory(10)->create([
            'user_id' => $user2->id
        ]);
        

        $url = route('api.v1.wishlist.index');
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user1->id,
        ])->getJson($url)
            ->assertOk()
            ->assertJson([
                $wishLists[0]->book->isbn,
                $wishLists[1]->book->isbn,
                $wishLists[2]->book->isbn,
                $wishLists[3]->book->isbn,
                $wishLists[4]->book->isbn,
                $wishLists[5]->book->isbn,
                $wishLists[6]->book->isbn,
                $wishLists[7]->book->isbn,
                $wishLists[8]->book->isbn,
                $wishLists[9]->book->isbn,
            ]);
    }

    /** @test */
    public function ほしいものリスト一覧_リストが存在しない場合(): void
    {
        $user1 = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $user2 = User::factory()->create([
            'access_token'        => str_repeat('b', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        WishList::factory(10)->create([
            'user_id' => $user1->id
        ]);

        $url = route('api.v1.wishlist.index');
        $this->withHeaders([
            'access_token' => str_repeat('b', 64),
            'user_id'      => $user2->id,
        ])->getJson($url)
            ->assertOk()
            ->assertJson([]);
    }
}
