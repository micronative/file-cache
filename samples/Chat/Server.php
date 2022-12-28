<?php

namespace Samples\Chat;
use Micronative\FileCache\CachePool;
use Samples\Chat\Services\ChatService;
use Samples\Chat\Services\UserService;

class Server
{
    private UserService $userService;
    private ChatService $chatService;

    public function __construct(UserService $userService = null, ChatService $chatService = null)
    {
        $this->userService = $userService ?? new UserService();
        $this->chatService = $chatService ?? new ChatService(__DIR__.'/Storage');
    }

    /**
     * @route api.chat.com/authenticate
     * @param string $username
     * @param $password
     * @return false|string
     */
    public function authenticate(string $username, $password): bool|string
    {
        return $this->userService->authenticate($username, $password);
    }

    /**
     * @route api.chat.com/start
     * @param array $participantsIds
     * @return string
     */
    public function start(array $participantsIds): string
    {
        return $this->chatService->start($participantsIds);
    }

    /**
     * @route api.chat.com/send
     * @param int $userId
     * @param string $conversationId
     * @param string $content
     * @return string
     */
    public function send(int $userId, string $conversationId, string $content)
    {
        return $this->chatService->send($userId, $conversationId, $content);
    }

    /**
     * @route api.chat.com/fetch
     * @param string $conversationId
     * @param int $lastMessageId
     * @return string
     */
    public function fetch(string $conversationId, int $lastMessageId): string
    {
        return $this->chatService->fetch($conversationId, $lastMessageId);
    }

    public function add(string $conversationId, int $userId)
    {
        return $this->chatService->add($conversationId, $userId);
    }
}