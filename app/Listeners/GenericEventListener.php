<?php

namespace App\Listeners;

use App\Events\GenericEvent;
use App\Helpers\ResponseHelper;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use ReflectionClass;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Http\Response;

class GenericEventListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\GenericEvent  $event
     * @return void
     */
    public function handle(GenericEvent $event)
    {
        $ipAddress = FacadeRequest::getClientIp();

        $client = new Client();
        $url = "http://localhost:9595/api/v1/logs";

        $headers = [
            'Content-Type' => 'application/json', // Example header
        ];

        
            // Accessing the protected or private property using reflection
            $reflectionClass = new ReflectionClass($event);

            $message = $reflectionClass->getProperty('subject');
            $route = $reflectionClass->getProperty('route');
            $method = $reflectionClass->getProperty('method');
            $descrition = $reflectionClass->getProperty('descrition');
            
            $message->setAccessible(true);
            $route->setAccessible(true);
            $method->setAccessible(true);
            $descrition->setAccessible(true);
            
            // dd($message->getValue($event), $route->getValue($event),  $method->getValue($event), json_encode($descrition->getValue($event)));

            $message = $message->getValue($event);
            $route = $route->getValue($event);
            $method = $method->getValue($event);
            $descrition = json_encode($descrition->getValue($event));

        // Send the POST request to the API
        $response = $client->request('POST', $url,
        [
            'form_params' => [
                'subject' => $message,
                'route' => $route,
                'method' => $method[0],
                'description' => $descrition,
                'visitor' => $ipAddress,
                'user_id' => 2
            ],
        ]);

        return $response->getStatusCode();
    }
}
