<?php

namespace Samples\Chat\Server\ChatService\Services;

use Exception;
use Psr\Cache\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Samples\Chat\Server\ChatService\Models\Conversation;
use Samples\Chat\Server\ChatService\Models\Message;

class ConversationManager
{
    /** @var Conversation[] */
    private array $conversations;
    private KeyManager $keyManager;
    private PersistentManager $persistentManager;

    /**
     * @param array $conversations
     * @param KeyManager|null $keyManager
     * @param PersistentManager|null $persistentManager
     */
    public function __construct(array $conversations = [], KeyManager $keyManager = null, PersistentManager $persistentManager = null)
    {
        $this->conversations = $conversations;
        $this->keyManager = $keyManager ?? new KeyManager();
        $this->persistentManager = $persistentManager ?? new PersistentManager();
    }

    /**
     * @param string $conversationId
     * @return Conversation|false
     */
    public function findConversationById(string $conversationId): Conversation|false
    {
        if (isset($this->conversations[$conversationId])) {
            return $this->conversations[$conversationId];
        }

        return false;
    }

    /**
     * @param Conversation $conversation
     * @return ConversationManager
     */
    public function stack(Conversation $conversation): ConversationManager
    {
        $this->conversations[$conversation->getId()] = $conversation;
        return $this;
    }

    /**
     * @param array $participantIds
     * @return Conversation|bool
     */
    public function findActiveConversation(array $participantIds): Conversation|bool
    {
        foreach ($this->conversations as $conversation) {
            if ($this->keyManager->key($participantIds) == $this->keyManager->key($conversation->getParticipantIds())) {
                return $conversation;
            }
        }

        return false;
    }

    /**
     * @param Conversation $conversation
     * @param int $lastMessageId
     * @return array
     */
    public function fetchConversationMessages(Conversation $conversation, int $lastMessageId): array
    {
        $messages = [];
        foreach ($conversation->getMessages() as $message) {
            if ($lastMessageId < $message->getOrder()) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * @return void
     */
    public function saveConversation(): void
    {
        $this->persistentManager->saveConversations($this->conversations);
    }

    /**
     * @param array $participantIds
     * @return Conversation
     * @throws Exception|InvalidArgumentException
     */
    public function findConversationByParticipantIds(array $participantIds): Conversation
    {
        if ($conversation = $this->findActiveConversation($participantIds)) {

            return $conversation;
        }

        if ($conversation = $this->persistentManager->loadConversation($participantIds)) {
            $this->stack($conversation);

            return $conversation;
        }

        $conversation = $this->createNewConversation($participantIds);
        $this->stack($conversation);

        return $conversation;
    }

    /**
     * @param array $participantIds
     * @return Conversation
     * @throws Exception
     */
    public function createNewConversation(array $participantIds): Conversation
    {
        return new Conversation(Uuid::uuid4()->toString(), $participantIds);
    }

    /**
     * @param Conversation $conversation
     * @param int $userId
     * @param string $content
     * @return Message
     */
    public function createNewMessage(Conversation $conversation, int $userId, string $content): Message
    {
        $lastMessageOrder = $this->getLastMessageOrder($conversation);
        return new Message($lastMessageOrder + 1, $userId, $content);
    }

    /**
     * @param Conversation $conversation
     * @return int
     */
    private function getLastMessageOrder(Conversation $conversation): int
    {
        $order = 0;
        foreach ($conversation->getMessages() as $message) {
            if ($order < $message->getOrder()) {
                $order = $message->getOrder();
            }
        }

        return $order;
    }
}