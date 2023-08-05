<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewPutRequest;
use App\Http\Requests\ReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
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

            return response('登録成功', Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response('登録失敗', Response::HTTP_INTERNAL_SERVER_ERROR);
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
