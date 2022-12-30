<?php

namespace Samples\Chat\Server\Chat\Services;

use Micronative\FileCache\CacheItem;
use Micronative\FileCache\CachePool;
use Micronative\FileCache\Exceptions\CachePoolException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Samples\Chat\Server\Chat\Models\Conversation;
use Samples\Chat\Server\Chat\Transformers\ConversationTransformer;

class PersistentManager
{
    private string $storageDir = __DIR__ . '/../Storage';
    private KeyManager $keyManager;
    private CacheItemPoolInterface $cachePool;
    private ConversationTransformer $conversationTransformer;

    /**
     * @param ConversationTransformer|null $conversationTransformer
     * @throws CachePoolException
     */
    public function __construct(KeyManager $keyManager = null, ConversationTransformer $conversationTransformer = null)
    {
        $this->cachePool = new CachePool($this->storageDir);
        $this->keyManager = $keyManager ?? new KeyManager();
        $this->conversationTransformer = $conversationTransformer ?? new ConversationTransformer();
    }

    /**
     * @param array $participantIds
     * @return Conversation|bool
     * @throws CachePoolException
     * @throws InvalidArgumentException
     */
    public function loadConversation(array $participantIds): Conversation|bool
    {
        $cacheItem = $this->cachePool->getItem($this->keyManager->key($participantIds));
        if ($cacheItem->get() !== null) {
            return $this->conversationTransformer->transform($cacheItem->get());
        }

        return false;
    }

    /**
     * @param array $conversations
     * @return void
     */
    public function saveConversations(array $conversations): void
    {
        foreach ($conversations as $conversation) {
            /** @var Conversation $conversation */
            $participantIds = $conversation->getParticipantIds();
            $this->cachePool->save(new CacheItem(['key' => $this->keyManager->key($participantIds), 'value' => $conversation->jsonSerialize()]));
        }
    }
}