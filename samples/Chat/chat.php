<?php
require_once('./vendor/autoload.php');

use Samples\Chat\Client\Client;
use Samples\Chat\Server\Api\Server;

$server = new Server();


try {
    $client1 = new Client($server);
    $client1->login('ken@chat.com', '123');

    $client2= new Client($server);
    $client2->login('may@chat.com', '123');

    $client1->startConversation([2]);
    $client1->sendMessage('Hello');

    $client2->startConversation([1]);
    $client2->sendMessage('Yes');
    $client1->poll();
    $client1->sendMessage('What is your name?');
    $client2->poll();
    $client2->sendMessage('My name is May. What about you?');
    $client1->poll();
    $client1->sendMessage('My name is Ken');
    $client2->poll();
    $client1->poll();
}catch (\Exception $exception) {
    echo $exception->getMessage();
}
