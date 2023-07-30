<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Book;
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
}
