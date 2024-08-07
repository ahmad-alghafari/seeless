<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['id' ,'resturant_id' ,'name'];

    public function BelongToResturant():BelongsTo{
        return $this->belongsTo(Resturant::class);
    }

    
}
