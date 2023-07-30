<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use \Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function login(LoginRequest $request): Response
    {
        if($this->isLogin($request->email, $request->password)) {

            $user = User::where('email', $request->email)->first();
            $user->access_token = $this->getAccessToken();
            $user->access_token_expire = Carbon::now()->addMonthNoOverflow()->toDateTimeString();
            $user->save();

            return response()
                ->json([
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'access_token' => $user->access_token
            ], Response::HTTP_OK);
        }

        return response('', Response::HTTP_FORBIDDEN);
    }

    private function isLogin(string $email, string $password): bool
    {
        $user = User::where('email', $email)->first();
        
        if ($user) {
            return password_verify($password, $user->password);
        }

        return false;
    }

    private function getAccessToken(): string
    {
        $token = Str::random(config('auth.token_size'));

        while(User::where('access_token', $token)->exists()) {
            $token = Str::random(config('auth.token_size'));
        }

        return $token;
    }
}
