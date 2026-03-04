<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class post extends Model
{
        use SoftDeletes;
    protected $fillable = ['caption', 'user_id'];
    protected $with = ['attachments', 'user'];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany {
        return $this->hasMany(post_attachment::class);
    }
}
