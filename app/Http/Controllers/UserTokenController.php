<?php

namespace App\Http\Controllers;

use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserTokenController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        $userIp = $request->ip();
        if ($userIp) {
            $userTokens = UserToken::query()
                ->where('ip', $userIp)
                ->where('created_at', '>', Carbon::now()->subDay())
                ->get();
            if ($userTokens->count() < 5) {
                /** @var UserToken $token */
                $token = UserToken::query()->create([
                    'ip' => $userIp,
                    'token' => Str::uuid()
                ]);
                return response()->json([
                    'status' => 0,
                    'response' => [
                        'token' => $token->token
                    ]
                ]);
            }
        }
        return response()->json([
            'status' => -1,
            'response' => [
                'message' => 'Reached limit for tokens, try it next day'
            ]
        ]);
    }

}
