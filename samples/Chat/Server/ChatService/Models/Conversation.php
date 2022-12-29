<?php

namespace Samples\Chat\Server\ChatService\Models;

class Conversation implements \JsonSerializable
{
    private string $id;
    private array $participantIds;
    /** @var Message[] */
    private array $messages;

    public function __construct(string $id, array $participantIds, array $messages = [])
    {
        $this->id = $id;
        $this->participantIds = $participantIds;
        $this->messages = $messages;
        sort($this->participantIds);
    }

    public function push(Message $message)
    {
        $this->messages[] = $message;
    }

    public function add(int $userId){
        $this->participantIds[] = $userId;
        sort($this->participantIds);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getId(): string
    {
        return $this->id;
    }

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