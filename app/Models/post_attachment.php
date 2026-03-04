<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class post_attachment extends Model
{
    protected $fillable = ['storage_path', 'post_id'];

  
}