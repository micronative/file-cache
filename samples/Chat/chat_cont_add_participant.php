<?php
require_once('./vendor/autoload.php');

use Samples\Chat\Client\Client;
use Samples\Chat\Server\Api\Server;

$server = new Server();


try {
    $client1 = new Client($server);
    $client1->login('ken@chat.com', '123');
    $client1->startConversation([2]);
    $client1->addParticipant(3);

    $client3 = new Client($server);
    $client3->login('tif@chat.com', '123');
    $client3->startConversation([1,2]);
    $client3->poll();
}catch (\Exception $exception) {
    echo $exception->getMessage();
}
