<?php

namespace App\Console\Commands\CrossServer;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Plugin\Events\Remote\RemoteMessageReceivedEvent;

class EventListen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cs:event:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen and propagate cross server events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Redis::subscribe(['*'], function (string $message, string $channel) {
            if (strpos($channel, ':') === false) {
                return;
            }

            [$namespace, $key] = explode(':', $channel);
            $event = new RemoteMessageReceivedEvent($namespace, $key, $message);
            $event->call();
        });
    }
}
