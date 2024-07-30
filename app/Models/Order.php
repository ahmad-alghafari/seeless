<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['id' , 'resturant_id' ,'table_number'];

    public function Content():HasMany{
        return $this->hasMany(OrderContent::class);
    }

    public function belongsToResturant():BelongsTo{
        return $this->belongsTo(Resturant::class);
    }
}
