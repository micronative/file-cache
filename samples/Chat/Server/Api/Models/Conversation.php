<?php

namespace Samples\Chat\Server\Api\Models;

class Conversation implements \JsonSerializable
{
    private string $id;

    /** @var User[] */
    private array $participants;

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
        $this->participants = $participantIds;
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
        $this->participants[] = $userId;
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
     * @return User[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'participants' => $this->getParticipants(),
            'messages' => $this->getMessages(),
        ];
    }
}