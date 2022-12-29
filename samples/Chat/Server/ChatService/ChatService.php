<?php

namespace Samples\Chat\Server\ChatService;

use Micronative\FileCache\CacheItem;
use Micronative\FileCache\CachePool;
use Psr\Cache\CacheItemPoolInterface;
use Ramsey\Uuid\Uuid;
use Samples\Chat\Server\ChatService\Models\Conversation;
use Samples\Chat\Server\ChatService\Models\Message;
use Samples\Chat\Server\ChatService\Transformers\ConversationTransformer;

class ChatService
{
    private string $storageDir = __DIR__ . '/Storage';
    private CacheItemPoolInterface $cachePool;
    private ConversationTransformer $conversationTransformer;

    /** @var Conversation[] */
    private array $conversations = [];

    public function __construct(ConversationTransformer $conversationTransformer = null)
    {
        $this->conversationTransformer = $conversationTransformer ?? new ConversationTransformer();
        $this->cachePool = new CachePool($this->storageDir);
    }

    public function __destruct()
    {
        $this->persistConversations();
    }

    /**
     * @route chat.internal.chat.com/start
     * @param array $participantIds
     * @return string
     */
    public function start(array $participantIds): string
    {
        $conversation = $this->findConversationByParticipantIds($participantIds);
        $this->stack($conversation);

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
        $conversation = $this->findConversationById($conversationId);
        $messages = $this->fetchConversationMessages($conversation, $lastMessageId);

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
        $conversation = $this->findConversationById($conversationId);
        $lastMassageOrder = $this->getLastMessageOrder($conversation);
        $message = new Message($lastMassageOrder + 1, $userId, $content);
        $conversation->push($message);

        return json_encode($message);
    }

    /**
     * @route chat.internal.chat.com/add
     * @param string $conversationId
     * @param int $userId
     * @return void
     */
    public function add(string $conversationId, int $userId)
    {
        $conversation = $this->findConversationById($conversationId);
        $conversation->add($userId);
    }

    private function fetchConversationMessages(Conversation $conversation, int $lastMessageId): array
    {
        $messages = [];
        foreach ($conversation->getMessages() as $message) {
            if ($lastMessageId < $message->getOrder()) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    private function findConversationById(string $conversationId): Conversation|false
    {
        if (isset($this->conversations[$conversationId])) {
            return $this->conversations[$conversationId];
        }

        return false;
    }

    private function findConversationByParticipantIds(array $participantIds): Conversation
    {
        if ($conversation = $this->findActiveConversation($participantIds)) {

            return $conversation;
        }

        if ($conversation = $this->loadConversation($participantIds)) {
            $this->stack($conversation);

            return $conversation;
        }

        $conversation = $this->createNewConversation($participantIds);
        $this->stack($conversation);

        return $conversation;
    }

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

    private function persistConversations(): void
    {
        foreach ($this->conversations as $conversation) {
            /** @var Conversation $conversation */
            $participantIds = $conversation->getParticipantIds();
            $cacheItem = new CacheItem(['key' => $this->key($participantIds), 'value' => $conversation->jsonSerialize()]);
            $this->cachePool->save($cacheItem);
        }
    }

    private function key(array $participants)
    {
        sort($participants);
        return md5(implode($participants));
    }

    private function stack(Conversation $conversation): void
    {
        $this->conversations[$conversation->getId()] = $conversation;
    }

    private function loadConversation(array $participantIds): Conversation|bool
    {
        $cacheItem = $this->cachePool->getItem($this->key($participantIds));
        if ($cacheItem->get() !== null) {
            return $this->conversationTransformer->transform($cacheItem->get());
        }

        return false;
    }

    private function findActiveConversation(array $participantIds): Conversation|bool
    {
        foreach ($this->conversations as $conversation) {
            if ($this->key($participantIds) == $this->key($conversation->getParticipantIds())) {
                return $conversation;
            }
        }

        return false;
    }

    private function createNewConversation(array $participantIds): Conversation
    {
        return new Conversation(Uuid::uuid4()->toString(), $participantIds);
    }
}
