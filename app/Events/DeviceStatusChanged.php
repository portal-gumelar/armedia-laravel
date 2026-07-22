<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $deviceType; // 'OLT' or 'MikroTik'
    public string $deviceName;
    public string $customerName;
    public string $status;
    public string $ipOrSn;
    
    /**
     * Create a new event instance.
     */
    public function __construct(string $deviceType, string $deviceName, string $customerName, string $status, string $ipOrSn)
    {
        $this->deviceType = $deviceType;
        $this->deviceName = $deviceName;
        $this->customerName = $customerName;
        $this->status = $status;
        $this->ipOrSn = $ipOrSn;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
