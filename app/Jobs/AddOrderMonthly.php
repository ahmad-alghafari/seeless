<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MonthlyOrder;
use App\Models\Resturant;
use App\Models\MonthlyOrderContent;
class AddOrderMonthly implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(
        public $resturant_id ,
        public $table_number ,
        public $order_contents ,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = MonthlyOrder::create([
            'resturant_id' => $this->resturant_id ,
            'table_number' => $this->table_number ,
        ]);

        $order_content_keys = array_keys($this->order_contents);

        foreach($order_content_keys as $ock){
            MonthlyOrderContent::create([
                'order_id' => $order->id , 
                'food_id' => $ock , 
                'count' => $this->order_contents[$ock],
            ]);
        }

        Resturant::where('id', $this->resturant_id)
            ->increment('orders_number');
    }
}
