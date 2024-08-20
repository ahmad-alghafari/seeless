<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Str;

class MonthlyOrder extends Model
{
    use HasFactory;

    protected $fillable = ['id' , 'resturant_id' ,'table_number'];

    protected $keyType = 'string'; // Ensure the primary key is treated as a string
    public $incrementing = false; // Disable auto-incrementing

    protected static function boot()
    {
        parent::boot();

        // Automatically generate UUID when creating a new model
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    public function Content():HasMany{
        return $this->hasMany(MonthlyOrderContent::class , 'order_id');
    }

    public function Resturant() : BelongsTo {
        return $this->belongsTo(Resturant::class);
    }


}
