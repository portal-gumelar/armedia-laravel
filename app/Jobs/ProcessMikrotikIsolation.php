<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Services\MikrotikService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMikrotikIsolation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customer;
    public $action; // 'isolate' or 'unisolate'

    /**
     * Create a new job instance.
     */
    public function __construct(Customer $customer, string $action = 'isolate')
    {
        $this->customer = $customer;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(MikrotikService $mikrotikService): void
    {
        if ($this->action === 'isolate') {
            $mikrotikService->isolateCustomer($this->customer);
        } else {
            $mikrotikService->unisolateCustomer($this->customer);
        }
    }
}
