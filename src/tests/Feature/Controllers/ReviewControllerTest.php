<?php

namespace Tests\Feature\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function レビュー投稿処理_book登録なし(): void
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
            'user_id'      => $user->id,
        ])->postJson($url, $param)
            ->assertStatus(201);
        
        $this->assertDatabaseHas('reviews',[
            'user_id' => $user->id,
            'comment' => $param['comment'],
            'point'   => $param['point'],
        ]);
    }

    /** @test */
    public function レビュー投稿処理_book登録あり(): void
    {
        $user = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $book = Book::factory()->create([
            'isbn' => '978-86354-417-8',
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
            ->assertStatus(201);
        
        $this->assertDatabaseHas('reviews',[
            'user_id' => $user->id,
            'book_id' => $book->id,
            'comment' => $param['comment'],
            'point'   => $param['point'],
        ]);
    }

    /** @test */
    public function レビュー更新処理(): void
    {
        $user   = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $book   = Book::factory()->create([
            'isbn' => '978-86354-417-8',
        ]);
        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $param = [
            'comment' => str_repeat('お', 200),
            'point'   => 4.5,
        ];

        $url = route('api.v1.review.update', $review);
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->putJson($url, $param)
            ->assertOk();
        
        $this->assertDatabaseHas('reviews',[
            'id'      => $review->id,
            'user_id' => $user->id,
            'comment' => $param['comment'],
            'point'   => $param['point'],
        ]);
    }

    /** @test */
    public function レビュー更新処理_ユーザーが異なる場合(): void
    {
        $user1  = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $user2  = User::factory()->create([
            'access_token'        => str_repeat('b', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $book   = Book::factory()->create([
            'isbn' => '978-86354-417-8',
        ]);
        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user1->id,
        ]);

        $param = [
            'comment' => str_repeat('お', 200),
            'point'   => 4.5,
        ];

        $url = route('api.v1.review.update', $review);
        $this->withHeaders([
            'access_token' => str_repeat('b', 64),
            'user_id'      => $user2->id,
        ])->putJson($url, $param)
            ->assertForbidden();
    }

    /** @test */
    public function レビュー削除処理(): void
    {
        $user   = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $book   = Book::factory()->create([
            'isbn' => '978-86354-417-8',
        ]);
        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $url = route('api.v1.review.update', $review);
        $this->withHeaders([
            'access_token' => str_repeat('a', 64),
            'user_id'      => $user->id,
        ])->deleteJson($url)
            ->assertOk();
        
        $this->assertDatabaseMissing('reviews',[
            'id'      => $review->id,
        ]);
    }

    /** @test */
    public function レビュー削除処理_ユーザーが異なる場合(): void
    {
        $user1  = User::factory()->create([
            'access_token'        => str_repeat('a', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $user2  = User::factory()->create([
            'access_token'        => str_repeat('b', 64),
            'access_token_expire' => Carbon::now()->addMonthNoOverflow()->toDateTimeString(),
        ]);
        $book   = Book::factory()->create([
            'isbn' => '978-86354-417-8',
        ]);
        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user1->id,
        ]);

        $url = route('api.v1.review.update', $review);
        $this->withHeaders([
            'access_token' => str_repeat('b', 64),
            'user_id'      => $user2->id,
        ])->deleteJson($url)
            ->assertForbidden();
    }
}
