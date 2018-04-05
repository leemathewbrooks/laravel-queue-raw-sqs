<?php

namespace PawprintDigital\LaravelQueueRawSqs\Jobs;

use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;
use Illuminate\Queue\CallQueuedHandler;
use Illuminate\Queue\InvalidPayloadException;
use Illuminate\Queue\Jobs\SqsJob;

class RawSqsJob extends SqsJob {

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $routes;

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Aws\Sqs\SqsClient  $sqs
     * @param  array   $job
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(Container $container, SqsClient $sqs, array $job, $connectionName, $queue, $routes)
    {
        parent::__construct($container, $sqs, $job, $connectionName, $queue);
        $this->routes = collect($routes);
    }

    /**
     * Get the name of the queued job class.
     *
     * @return string
     */
    public function getName()
    {
        return $this->payload()['TopicArn'];
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {

        $rawPayload = $this->payload();
        $topic = $this->getTopicFromPayload($rawPayload);
        $message = $this->getDecodedMessageFromPayload($rawPayload);
        $topicClass = $this->getTopicClass($topic, $message);
        $serializedClass = serialize($topicClass);

        $data = [
            'command' => $serializedClass
        ];

        $class = CallQueuedHandler::class;

        ($this->instance = $this->resolve($class))->call($this, $data);
    }

    protected function getTopicClass($topic, $message)
    {
        $filtered = $this->routes->filter(function($routeClass, $routeTopic) use ($topic){
            if (fnmatch($routeTopic, $topic)) return true;
        });

        if ($filtered->count()){
            $className = $filtered->first();
        }
        else {
            $className = 'App\\Jobs\\'.$topic;
        }

        return $this->container->make($className, ['data' => $message]);
    }

    protected function getTopicFromPayload($payload)
    {
        if (isset($payload['TopicArn'])) {
            return last(explode(':', $payload['TopicArn']));
        }
        else {
            throw new InvalidPayloadException('Message payload does not contain \'TopicArn\'');
        }
    }

    protected function getDecodedMessageFromPayload($payload)
    {
        if (isset($payload['Message'])) {
            return json_decode($payload['Message'], true);
        }
        else {
            throw new InvalidPayloadException('Message payload does not contain \'Message\'');
        }
    }

}