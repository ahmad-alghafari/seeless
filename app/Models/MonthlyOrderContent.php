<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MonthlyOrderContent extends Model
{
    use HasFactory;
    protected $fillable = ['id' ,'order_id' , 'food_id' , 'count'];

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
}
