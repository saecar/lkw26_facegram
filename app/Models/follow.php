<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class follow extends Model
{
    protected $fillable = ['follower_id', 'following_id', 'is_accepted'];

    protected function casts(): array
    {
        return ['is_accepted' => 'boolean'];
    }

    public function follower() : BelongsTo{
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following() : BelongsTo{
        return $this->belongsTo(User::class, 'following_id');
    }
}
