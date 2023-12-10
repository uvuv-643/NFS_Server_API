<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property $user_id_token integer
 * @property $inode integer
 * @property $name string
 * @property $type string
 * @property $parent_id integer
 * @property $hard_link_id integer
 * @property $children Collection
 */
class Node extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function children() : HasMany
    {
        return $this->hasMany(Node::class, 'parent_id', 'id');
    }

}
