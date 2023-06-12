<?php

namespace App\Helpers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final class ActivityLogManager
{
    public static function sendMessageLog($messageBody)
    {
        $exchangeName="cds_service_exchange"; 
        $routingKey="cds_service_routing_key";
        
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('cds_task_queue', false, true, false, false);
        $channel->exchange_declare($exchangeName, 'direct', false, true, false);
        $channel->queue_bind('cds_task_queue', $exchangeName, $routingKey);

        // Declare the exchange and routing key
        $message = new AMQPMessage($messageBody);
        $channel->basic_publish($message, $exchangeName, $routingKey);

        // Close the channel and connection
        $channel->close();
        $connection->close();
    }
}
