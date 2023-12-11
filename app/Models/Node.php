<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id_token
 * @property int $inode
 * @property string $name
 * @property string $content
 * @property string $type
 * @property int $parent_id
 * @property int $hard_link_id
 * @property Collection $children
 */
class Node extends Model
{

    protected $guarded = [];

    public function children() : HasMany
    {
        return $this->hasMany(Node::class, 'parent_id', 'id');
    }

}
