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

    /** @test */
    public function レビュー一覧(): void
    {
        $book1    = Book::factory()->create([
            'isbn' => '978-86354-417-8',
        ]);
        $book2    = Book::factory()->create([
            'isbn' => '978-86354-417-9',
        ]);
        $reviews1 = Review::factory(10)->create([
            'book_id' => $book1->id,
        ]);
        $reviews2 = Review::factory(5)->create([
            'book_id' => $book2->id,
        ]);

        $url = route('api.v1.review.index', ['isbn' => ['978-86354-417-8', '978-86354-417-9']]);
        $this->getJson($url)
            ->assertOk()
            ->assertJson([
                '978-86354-417-8' => [
                    'isbn'  => '978-86354-417-8',
                    'count' => 10,
                    'point' => $reviews1->avg('point'),
                ],
                '978-86354-417-9' => [
                    'isbn'  => '978-86354-417-9',
                    'count' => 5,
                    'point' => $reviews2->avg('point'),
                ],
            ]);
    }

    /** @test */
    public function レビュー一覧_レビューが存在しない場合(): void
    {
        $book1    = Book::factory()->create([
            'isbn' => '978-86354-417-8',
        ]);
        $book2    = Book::factory()->create([
            'isbn' => '978-86354-417-9',
        ]);
        $reviews1 = Review::factory(10)->create([
            'book_id' => $book1->id,
        ]);
        $reviews2 = Review::factory(5)->create([
            'book_id' => $book2->id,
        ]);

        $url = route('api.v1.review.index', ['isbn' => ['978-86354-417-1', '978-86354-417-2']]);
        $this->getJson($url)
            ->assertOk()
            ->assertJson([]);
    }

    /** @test */
    public function レビュー詳細(): void
    {
        $book    = Book::factory()->create([
            'isbn'    => '978-86354-417-8',
        ]);
        $reviews = Review::factory(3)->create([
            'book_id' => $book->id,
        ]);

        $url = route('api.v1.review.show', ['isbn' => $book->isbn]);
        $this->getJson($url)
            ->assertOk()
            ->assertJson([
                [
                    'user_id'   => $reviews[0]->user_id,
                    'user_name' => $reviews[0]->user->name,
                    'comment'   => $reviews[0]->comment,
                    'point'     => $reviews[0]->point,
                ],
                [
                    'user_id'   => $reviews[1]->user_id,
                    'user_name' => $reviews[1]->user->name,
                    'comment'   => $reviews[1]->comment,
                    'point'     => $reviews[1]->point,
                ],
                [
                    'user_id'   => $reviews[2]->user_id,
                    'user_name' => $reviews[2]->user->name,
                    'comment'   => $reviews[2]->comment,
                    'point'     => $reviews[2]->point,
                ],
            ]);
    }

    /** @test */
    public function レビュー詳細_isbnが無効な値(): void
    {
        $book = Book::factory()->create([
            'isbn' => '978-86354-417-8',
        ]);
        Review::factory(3)->create([
            'book_id' => $book->id,
        ]);

        $url = route('api.v1.review.show', ['isbn' => 'aaaa']);
        $this->getJson($url)
            ->assertOk()
            ->assertJson([]);
    }
}
