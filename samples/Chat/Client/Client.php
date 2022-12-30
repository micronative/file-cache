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
use Samples\Chat\Server\Api\Server;

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

    /**
     * @param Server $server
     * @param UserTransformer|null $userTransformer
     * @param ConversationTransformer|null $conversationTransformer
     * @param MessageTransformer|null $messageTransformer
     * @param MessageMapper|null $messageMapper
     * @param ChatTable|null $chatTable
     */
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

    /**
     * User login
     * @param string $username
     * @param string $password
     * @return void
     */
    public function login(string $username, string $password): void
    {
        $json = $this->server->authenticate($username, $password);
        if (empty($json)) {
            throw new UserException('Invalid username or password');
        }

        $this->loggedInUser = $this->userTransformer->transform($json);
    }

    /**
     * Start a conversation
     * @param array $participantIds
     * @return void
     */
    public function startConversation(array $participantIds): void
    {
        $this->requireLoggedInUser();
        $participantIds[] = $this->loggedInUser->getId();
        $json = $this->server->start($participantIds);
        $this->activeConversation = $this->conversationTransformer->transform($json);
    }

    /**
     * Send message to the current conversation
     * @param string $content
     * @return void
     */
    public function sendMessage(string $content): void
    {
        $this->requireActiveConversation();
        $this->server->send($this->loggedInUser->getId(), $this->activeConversation->getId(), $content);
    }

    /**
     * Add new participant to the current conversation
     * @param int $userId
     * @return void
     */
    public function addParticipant(int $userId): void
    {
        $this->requireActiveConversation();
        $this->server->add($this->activeConversation->getId(), $userId);
    }

    /**
     * Poll for new messages
     * @return void
     */
    public function poll(): void
    {
        $this->requireActiveConversation();
        $json = $this->server->fetch($this->activeConversation->getId(), $this->getLastMessageOrder());
        $data = json_decode($json, true);
        foreach ($data as $message) {
            $this->activeConversation->push($this->messageTransformer->transform(json_encode($message)));
        }
        error_log('-- Polling: ' . $json . PHP_EOL);
        $this->display();
    }

    /**
     * Display the current conversation
     * @return void
     */
    private function display(): void
    {
        $this->requireActiveConversation();
        $userData = json_decode(json_encode($this->activeConversation->getParticipants()), true);
        $messageData = json_decode(json_encode($this->activeConversation->getMessages()), true);
        $this->chatTable->render(['Order', 'At', 'User', 'Message'], $this->messageMapper->map($messageData, $userData));
    }

    /**
     * @return int
     */
    private function getLastMessageOrder(): int
    {
        $order = 0;
        foreach ($this->activeConversation->getMessages() as $message) {
            if ($order < $message->getOrder()) {
                $order = $message->getOrder();
            }
        }

        return $order;
    }

    /**
     * Check if user start the conversation or not
     * @return void
     */
    private function requireActiveConversation(): void
    {
        if (empty($this->activeConversation)) {
            throw new UserException('Please start conversation first.');
        }
    }

    /**
     * Check if user logged in or not
     * @return void
     */
    private function requireLoggedInUser(): void
    {
        if (empty($this->loggedInUser)) {
            throw new UserException('Please log in to first.');
        }
    }
}