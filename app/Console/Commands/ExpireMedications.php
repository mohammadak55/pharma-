<?php

namespace App\Console\Commands;

use App\Models\Medication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireMedications extends Command
{
    protected $signature = 'expire:medications';
    protected $description = 'Mark medications as expired based on expiry date';

    public function handle()
    {
        $now = Carbon::now();
        $expiredMedications = Medication::where('Expiry date', '<=', $now)->get();
        foreach ($expiredMedications as $medication) {
            $medication->update(['expired' => 'expired']);
        }
        $this->info("Medications marked as expired successfully.");
    }
}
