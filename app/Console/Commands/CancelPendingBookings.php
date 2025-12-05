<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CancelPendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel bookings that are still pending after 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoff = Carbon::now()->subMinutes(30);

        $cancelled = Booking::where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->update(['status' => 'cancelled']);

        $this->info("$cancelled pending bookings cancelled.");
    }
}
