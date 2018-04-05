<?php

namespace PawprintDigital\LaravelQueueRawSqs;

use Illuminate\Queue\Connectors\SqsConnector;
use Illuminate\Support\ServiceProvider;
use PawprintDigital\LaravelQueueRawSqs\Connectors\RawSqsConnector;

class LaravelQueueRawSqsServiceProvider extends ServiceProvider {
    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        // Nothing to do here
    }

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        /** @var \Illuminate\Queue\QueueManager $queue */
        $queue = $this->app['queue'];
        $queue->addConnector('rawsqs', function () {
            return new RawSqsConnector();
        });
    }


}