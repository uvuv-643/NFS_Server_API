<?php

namespace App\Http\Controllers;

use App\Models\Node;
use App\Models\UserToken;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserTokenController extends Controller
{

    const RESPONSE_SUCCESS = 0;
    const RESPONSE_FAILURE = -1;
    const USER_TOKEN_LIMIT = 500;

    public function store(Request $request): JsonResponse
    {
        $userIp = $request->ip();
        if ($userIp) {
            $userTokens = UserToken::query()
                ->where('ip', $userIp)
                ->where('created_at', '>', Carbon::now()->subDay())
                ->get();
            if ($userTokens->count() < self::USER_TOKEN_LIMIT) {
                /** @var UserToken $token */
                /** @var Node $node */
                $token = UserToken::query()->create([
                    'ip' => $userIp,
                    'token' => Str::uuid()
                ]);
                try {
                    $node = Node::query()->create([
                        'user_token_id' => $token->id,
                        'inode' => 0,
                        'name' => 'root',
                        'type' => 'directory',
                    ]);
                } catch (Exception $exception) {
                    $token->delete();
                    return response()->json([
                        'status' => self::RESPONSE_FAILURE,
                        'message' => $exception->getMessage()
                    ]);
                }

                return response()->json([
                    'status' => self::RESPONSE_SUCCESS,
                    'response' => [
                        'token' => $token->token,
                        'root_inode' => $node->inode
                    ]
                ]);
            }
        }
        return response()->json([
            'status' => self::RESPONSE_FAILURE,
            'response' => [
                'message' => 'Reached limit for tokens, try it next day'
            ]
        ]);
    }

}
