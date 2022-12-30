<?php

namespace Samples\Chat\Server\Chat;

use Samples\Chat\Server\Chat\Services\ConversationManager;

class ChatService
{
    private ConversationManager $conversationManager;

    public function __construct(ConversationManager $conversationManager = null)
    {
        $this->conversationManager = $conversationManager ?? new ConversationManager();
    }

    public function __destruct()
    {
        $this->conversationManager->saveConversation();
    }

    /**
     * @route chat.internal.chat.com/start
     * @param array $participantIds
     * @return string
     */
    public function start(array $participantIds): string
    {
        $conversation = $this->conversationManager->findConversationByParticipantIds($participantIds);
        $this->conversationManager->stack($conversation);

        return json_encode($conversation);
    }

    /**
     * @route chat.internal.chat.com/fetch
     * @param string $conversationId
     * @param int $lastMessageId
     * @return string
     */
    public function fetch(string $conversationId, int $lastMessageId): string
    {
        $conversation = $this->conversationManager->findConversationById($conversationId);
        $messages = $this->conversationManager->fetchConversationMessages($conversation, $lastMessageId);

        return json_encode($messages);
    }

    /**
     * @route chat.internal.chat.com/send
     * @param int $userId
     * @param string $conversationId
     * @param string $content
     * @return string
     */
    public function send(int $userId, string $conversationId, string $content): string
    {
        $conversation = $this->conversationManager->findConversationById($conversationId);
        $message = $this->conversationManager->createNewMessage($conversation, $userId, $content);
        $conversation->push($message);

        return json_encode($message);
    }

    /**
     * @route chat.internal.chat.com/add
     * @param string $conversationId
     * @param int $userId
     * @return void
     */
    public function add(string $conversationId, int $userId): void
    {
        $conversation = $this->conversationManager->findConversationById($conversationId);
        $conversation->add($userId);
    }
}
