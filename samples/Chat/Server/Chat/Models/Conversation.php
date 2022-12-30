<?php

namespace Samples\Chat\Server\Chat\Models;

class Conversation implements \JsonSerializable
{
    private string $id;
    private array $participantIds;
    /** @var Message[] */
    private array $messages;

    /**
     * @param string $id
     * @param array $participantIds
     * @param array $messages
     */
    public function __construct(string $id, array $participantIds, array $messages = [])
    {
        $this->id = $id;
        $this->participantIds = $participantIds;
        $this->messages = $messages;
    }

    /**
     * @param Message $message
     * @return Conversation
     */
    public function push(Message $message): Conversation
    {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @param int $userId
     * @return Conversation
     */
    public function add(int $userId): Conversation
    {
        $this->participantIds[] = $userId;
        return $this;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getParticipantIds(): array
    {
        return $this->participantIds;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'participant_ids' => $this->participantIds,
            'messages' => $this->messages
        ];
    }
}