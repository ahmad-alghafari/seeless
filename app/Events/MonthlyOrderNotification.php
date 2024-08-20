<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\MonthlyOrder;
class MonthlyOrderNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    
    
    public function __construct(
        public  $orders)
    {
        
    }

    public function broadcastOn(): array
    {
        return [new Channel('orders-resturant-'.$this->orders['user_id'])];
    }

    public function broadcastAs(): string
    {
        return 'OrderNotification';
    }

    public function broadcastWith() : array
    {
        return [
            'order' => $this->orders
        ];
    }
}
