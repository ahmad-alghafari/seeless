<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRcodeImage extends Model
{
    use HasFactory;

    protected $fillable = ['resturant_id' , 'path' , 'table_number'];
}
