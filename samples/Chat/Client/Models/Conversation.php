<?php

namespace Samples\Chat\Client\Models;

class Conversation implements \JsonSerializable
{
    private string $id;

    /** @var User[] */
    private array $participants;

    /** @var Message[] */
    private array $messages;

    public function __construct(string $id, array $participantIds, array $messages = [])
    {
        $this->id = $id;
        $this->participants = $participantIds;
        $this->messages = $messages;
        sort($this->participants);
    }

    public function push(Message $message)
    {
        $this->messages[] = $message;
    }

    public function add(int $userId){
        $this->participants[] = $userId;
        sort($this->participants);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParticipants(): array
    {
        return $this->participants;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'participants' => $this->participants,
            'messages' => $this->messages
        ];
    }
}