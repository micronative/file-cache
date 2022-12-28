<?php
require_once('./vendor/autoload.php');

use Samples\Chat\Server;
use Samples\Chat\Client;

$server = new Server();


try {
    $client1 = new Client($server);
    $client1->login('ken@chat.com', '123');

    $client2= new Client($server);
    $client2->login('may@chat.com', '123');

    $client1->startConversation([2]);
    $client2->startConversation([1]);
    $client1->poll();
    $client2->poll();
    $client1->sendMessage('Nice to meet you May');
    $client2->sendMessage('Mee too');
    $client1->poll();
    $client2->poll();
    $client1->display();
    $client2->display();
}catch (\Exception $exception) {
    echo $exception->getMessage();
}
