<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\WishListRequest;
use App\Models\Book;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;


class WishListController extends Controller
{
    public function store(WishListRequest $request)
    {
        $userId = $request->header('user_id');

        try {
            $book = Book::firstOrCreate(['isbn' => $request->isbn]);
            $book->wishLists()->create([
                'user_id' => $userId,
            ]);

            return response('登録成功', Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response('登録失敗', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
