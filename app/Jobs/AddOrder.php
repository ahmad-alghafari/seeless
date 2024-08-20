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
use App\Events\OrderNotification;
use App\Events\MonthlyOrderNotification;

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

            
        $order = $order->load(['Content', 'Resturant.User']);
        $orderData = [
            'id' => $order->id,
            'table_number' => $order->table_number,
            'created_at' => $order->created_at,
            'content' => $order->Content->map(function ($content) {
                return [
                    'food_id' => $content->food_id,
                    'count' => $content->count,
                ];
            }),
            'user_id' => $order->Resturant->User->id,
        ];
        MonthlyOrderNotification::dispatch($orderData);
        
    }
}
