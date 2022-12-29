<?php

namespace Samples\Chat\Client;

use Samples\Chat\Client\Display\ChatTable;
use Samples\Chat\Client\Display\MessageMapper;
use Samples\Chat\Client\Exceptions\UserException;
use Samples\Chat\Client\Models\Conversation;
use Samples\Chat\Client\Models\User;
use Samples\Chat\Client\Transformers\ConversationTransformer;
use Samples\Chat\Client\Transformers\MessageTransformer;
use Samples\Chat\Client\Transformers\UserTransformer;
use Samples\Chat\Server\PublicApi\Server;

class Client implements ClientInterface
{
    private User $loggedInUser;
    private Conversation $activeConversation;
    private Server $server;
    private UserTransformer $userTransformer;
    private ConversationTransformer $conversationTransformer;
    private MessageTransformer $messageTransformer;

    private MessageMapper $messageMapper;
    private ChatTable $chatTable;

    public function __construct(
        Server                  $server,
        UserTransformer         $userTransformer = null,
        ConversationTransformer $conversationTransformer = null,
        MessageTransformer      $messageTransformer = null,
        MessageMapper           $messageMapper = null,
        ChatTable               $chatTable = null)
    {
        $this->server = $server;
        $this->userTransformer = $userTransformer ?? new UserTransformer();
        $this->conversationTransformer = $conversationTransformer ?? new ConversationTransformer();
        $this->messageTransformer = $messageTransformer ?? new MessageTransformer();
        $this->messageMapper = $messageMapper ?? new MessageMapper();
        $this->chatTable = $chatTable ?? new ChatTable();
    }

    public function login(string $username, string $password)
    {
        if ($json = $this->server->authenticate($username, $password)) {
            $this->loggedInUser = $this->userTransformer->transform($json);

            return true;
        }

        throw new UserException('Invalid username or password');
    }

    public function startConversation(array $participantIds)
    {
        $participantIds[] = $this->loggedInUser->getId();
        $json = $this->server->start($participantIds);
        $this->activeConversation = $this->conversationTransformer->transform($json);
    }

    public function sendMessage(string $content)
    {
        $this->server->send($this->loggedInUser->getId(), $this->activeConversation->getId(), $content);
    }

    public function addParticipant(int $userId)
    {
        $this->server->add($this->activeConversation->getId(), $userId);
    }

    public function poll()
    {
        $json = $this->server->fetch($this->activeConversation->getId(), $this->getLastMessageOrder());
        $data = json_decode($json, true);
        foreach ($data as $message) {
            $this->activeConversation->push($this->messageTransformer->transform(json_encode($message)));
        }
        echo('-- Polling: ' . $json . PHP_EOL);
    }

    public function display()
    {
        $userData = json_decode(json_encode($this->activeConversation->getParticipants()), true);
        $messageData = json_decode(json_encode($this->activeConversation->getMessages()), true);
        $this->chatTable->render(['Order', 'At', 'User', 'Message'], $this->messageMapper->map($messageData, $userData));
    }

    private function getLastMessageOrder()
    {
        $order = 0;
        foreach ($this->activeConversation->getMessages() as $message) {
            if ($order < $message->getOrder()) {
                $order = $message->getOrder();
            }
        }

        return $order;
    }
}