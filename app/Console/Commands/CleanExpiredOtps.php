<?php

namespace App\Console\Commands;

use App\Models\PasswordOtp;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:clean-expired-otps';
    protected $signature = 'otp:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired OTPs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = PasswordOtp::where('expires_at', '<', now())->count();

        $this->info("Expired OTPs: $count");

        PasswordOtp::where('expires_at', '<', now())->delete();

        $this->info("Expired OTPs deleted successfully.");
    }
}
