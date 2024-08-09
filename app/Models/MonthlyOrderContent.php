<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyOrderContent extends Model
{
    use HasFactory;
    protected $fillable = ['id' ,'order_id' , 'food_id' , 'count'];

}
