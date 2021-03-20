<?php

namespace App\Console\Commands;

use App\Services\Properties\PropertiesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResetCacheProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reset-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear properties caches and set it again';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Cache::forget('allProperties');
        Cache::forget('zapProperties');
        Cache::forget('vivaRealProperties');
        $this->info('Cache cleared');
        (new PropertiesService)->setAllProperties()
                               ->setZapProperties()
                               ->setVivaRealProperties();
        $this->info('Cache setted');
        Mail::raw('Cache cleared', function($message)
        {
            $message->from(config('mail.from.address'), 'Cache cleared');
            $message->subject('Cache Cleared');
            $message->to(config('mail.from.address'));
        });
    }
}
