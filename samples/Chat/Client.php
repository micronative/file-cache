<?php

namespace Samples\Chat;

use Samples\Chat\Exceptions\UserException;
use Samples\Chat\Models\Conversation;
use Samples\Chat\Models\User;
use Samples\Chat\Transformers\ConversationTransformer;
use Samples\Chat\Transformers\MessageTransformer;
use Samples\Chat\Transformers\UserTransformer;

class Client implements ClientInterface
{
    private User $loggedInUser;
    private Conversation $activeConversation;
    private Server $server;
    private UserTransformer $userTransformer;
    private ConversationTransformer $conversationTransformer;
    private MessageTransformer $messageTransformer;

    public function __construct(Server $server, UserTransformer $userTransformer = null, ConversationTransformer $conversationTransformer = null, $messageTransformer = null)
    {
        $this->server = $server;
        $this->userTransformer = $userTransformer ?? new UserTransformer();
        $this->conversationTransformer = $conversationTransformer ?? new ConversationTransformer();
        $this->messageTransformer = $messageTransformer ?? new MessageTransformer();
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
        foreach ($data as $message){
            $this->activeConversation->push($this->messageTransformer->transform(json_encode($message)));
        }
        error_log($json);
    }

    public function display()
    {
        error_log(json_encode($this->activeConversation->getMessages()));
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