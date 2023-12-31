<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewPutRequest;
use App\Http\Requests\ReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Collection
    {
        $isbnArray = $request->input('isbn');
        $result = Book::with('reviews')
            ->selectRaw('books.isbn, count(*) AS count, avg(point) AS point')
            ->whereIn('books.isbn', $isbnArray)
            ->join('reviews', 'books.id', '=', 'reviews.book_id')
            ->groupBy('isbn')
            ->get()
            ->keyBy('isbn');
        
        return $result;
    }

    public function store(ReviewRequest $request)
    {
        $userId = $request->header('user_id');

        try {
            $book = Book::firstOrCreate(['isbn' => $request->isbn]);
            $book->reviews()->create([
                'user_id' => $userId,
                'comment' => $request->comment,
                'point'   => $request->point,
            ]);

            return response('登録成功', Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response('登録失敗', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $isbn)
    {
        $book = Book::with('reviews')->with('reviews.user')->where('isbn', $isbn)->first();

        if ($book) {
            return $book->reviews->map(function ($review) {
                return [
                    'user_id'   => $review->user_id,
                    'user_name' => $review->user->name,
                    'comment'   => $review->comment,
                    'point'     => $review->point,
                ];
            });
        }

        return [];
    }

    public function update(ReviewPutRequest $request, Review $review)
    {
        $userId = $request->header('user_id');
        if ((int) $userId !== $review->user_id) {
            return response('', Response::HTTP_FORBIDDEN);
        }

        try {
            $review->update([
                'comment' => $request->comment,
                'point'   => $request->point,
            ]);

            return response('更新成功', Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response('更新失敗', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, Review $review)
    {
        $userId = $request->header('user_id');
        if ((int) $userId !== $review->user_id) {
            return response('', Response::HTTP_FORBIDDEN);
        }

        try {
            $review->delete();

            return response('削除成功', Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response('削除失敗', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
