<?php

return [
    'driver' => 'rawsqs',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'prefix' => env('AWS_SQS_QUEUE_PREFIX'),
    'queue' => env('AWS_SQS_QUEUE_NAME'),
    'region' => env('AWS_REGION'),
    'routes' => [
        'Topic' => 'Apps\\Jobs\\JobHandler',
        'Topic*' => 'Apps\\Jobs\\JobHandler',
        '*Topic' => 'Apps\\Jobs\\JobHandler'
    ]
];