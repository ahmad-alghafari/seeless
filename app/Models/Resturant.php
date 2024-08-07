<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Resturant extends Model
{
    use HasFactory;

    protected $fillable = ['id' , 'name' ,'orders_number' ,'user_id' ,'tamplate_number','service_type','service_price','status',
    'path_website_hero','path_brand'];
    
    public function Order() : HasMany{
        return $this->HasMany(Order::class);
    }

    public function Category() : HasMany{
        return $this->HasMany(Category::class);
    }

    public function Food() : HasMany{
        return $this->HasMany(Food::class);
    }

    public function Qrcode() : HasMany{
        return $this->HasMany(Qrcodeimage::class);
    }

    

}
