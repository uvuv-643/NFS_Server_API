<?php

namespace App\Rules;

use App\Models\Node;
use App\Models\UserToken;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;


/**
 * @property $token string
 */
class InodeAbstractRule implements ValidationRule
{

    public ?string $token;

    public function __construct(?string $token)
    {
        $this->token = $token;
    }

    protected function getNode(string $inode) : Node | null
    {
        /** @var UserToken $userToken */
        /** @var Node | null $targetNode */
        $userToken = UserToken::query()->where('token', $this->token)->firstOrFail();
        $targetNode = Node::query()
            ->where('user_token_id', $userToken->id)
            ->whereNull('hard_link_id')
            ->where('inode', $inode)->first();;
        return $targetNode;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

    }

}
