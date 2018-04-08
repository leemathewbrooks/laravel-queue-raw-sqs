# Laravel Queue Driver For Raw SQS Messages

This queue driver will allow you to take raw JSON data from an SQS
queue that was received outside of Laravel (for example from an SNS
subscription) and map it to the correct job handler inside Laravel.

## Requirements

Laravel 5.6+


## Installation

You can install the package through [Composer](http://getcomposer.org/)
with the following command

```bash
composer require pawprintdigital/laravel-queue-raw-sqs
```

### Service Provider

The service provider should register with Laravel automatically
through Laravel 5.6's package discovery feature.

If this doesn't work, you can  manually register the service
provider by  adding the following line to the `providers`
array in your `config/app.php` file.

```php
PawprintDigital\LaravelQueueRawSqs\LaravelQueueRawSqsServiceProvider::class
```


## Configuration

To configure the package, add the following element
to the `connections` array in `config/queue.php`

```php
'rawsqs' => [
    'driver' => 'rawsqs',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'prefix' => env('AWS_SQS_QUEUE_PREFIX'),
    'queue' => env('AWS_SQS_QUEUE_NAME'),
    'region' => env('AWS_REGION'),
    'routes' => [
        '*' =>'App\\Jobs\\JobHandler'
    ]
]
```

### Routes

Messages off the queue are mapped by their SNS topic name. You will need
to modify the `routes` element of the array you added previously to
map a SNS Topic Name to a Job.

Note: You can use wildcards (*) in the topic name if you want to
ignore suffixes or prefixes in the Topic Name.

By default, if there is no route set in the configuration file,
the driver will attempt to map the TopicName to the same class
name in App\Jobs. For example a TopicName of UserCreated would
attempt to map to App\Jobs\UserCreated::class if there's no
 entry in the `routes` array.
