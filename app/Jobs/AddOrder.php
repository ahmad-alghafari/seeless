<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\OrderContent;
use App\Models\Resturant;

class AddOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        public $resturant_id ;
        public $table_number ;
        public $order_contents ;

    public function __construct(
        $res_id , 
        $tab_num ,
        $ord_cont ,
    )
    {
        $this->resturant_id = $res_id;
        $this->table_number =$tab_num;
        $this->order_contents = $ord_cont;
    }

    public function handle(): void
    {
        
        $order = Order::create([
            'resturant_id' => $this->resturant_id ,
            'table_number' => $this->table_number ,
        ]);

        $order_content_keys = array_keys($this->order_contents);

        foreach($order_content_keys as $ock){
            OrderContent::create([
                'order_id' => $order->id , 
                'food_id' => $ock , 
                'count' => $this->order_contents[$ock],
            ]);
        }

        Resturant::where('id', $this->resturant_id)
            ->increment('orders_number');
        
    }
}
