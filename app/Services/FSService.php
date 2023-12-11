<?php

namespace App\Services;

use App\Models\Node;

class FSService
{

    public function getNodeIdByInode(int $inode, int $userTokenId) : ?int
    {
        /** @var Node | null $node */
        $node = Node::query()
            ->where('user_token_id', $userTokenId)
            ->whereNull('hard_link_id')
            ->where('inode', $inode)->first();
        return $node?->id;
    }

}
