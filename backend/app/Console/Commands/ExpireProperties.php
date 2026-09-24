<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

class ExpireProperties extends Command
{
    protected $signature   = 'properties:expire';
    protected $description = 'Mark expired property listings as inactive';

    public function handle(): void
    {
        $count = Property::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'inactive']);

        $this->info("Expired {$count} properties.");
    }
}
