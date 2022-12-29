<?php

namespace Samples\Chat\Server\PublicApi;

use Samples\Chat\Server\ChatService\ChatService;
use Samples\Chat\Server\PublicApi\Transformers\ConversationTransformer;
use Samples\Chat\Server\UserService\UserService;

class Server
{
    private UserService $userService;
    private ChatService $chatService;

    private ConversationTransformer $conversationTransformer;

    public function __construct(UserService $userService = null, ChatService $chatService = null, ConversationTransformer $conversationTransformer = null)
    {
        $this->userService = $userService ?? new UserService();
        $this->chatService = $chatService ?? new ChatService();
        $this->conversationTransformer = $conversationTransformer ?? new ConversationTransformer();
    }

    /**
     * @route api.chat.com/authenticate
     * @param string $username
     * @param $password
     * @return string
     */
    public function authenticate(string $username, $password): string
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
        $conversationJson = $this->chatService->start($participantsIds);
        $userJson = $this->userService->get($participantsIds);
        return json_encode($this->conversationTransformer->transform($conversationJson, $userJson));
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

    /**
     * @route: api.chat.com/add
     * @param string $conversationId
     * @param int $userId
     * @return null
     */
    public function add(string $conversationId, int $userId)
    {
        return $this->chatService->add($conversationId, $userId);
    }
}