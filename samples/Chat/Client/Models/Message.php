<?php

namespace Samples\Chat\Client\Models;

class Message implements \JsonSerializable
{
    private int $order;
    private int $userId;
    private string $content;
    private string $createdAt;

    public function __construct(int $order, int $userId, string $content, string $createdAt)
    {
        $this->order = $order;
        $this->userId = $userId;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function jsonSerialize()
    {
        return [
            'order' => $this->getOrder(),
            'user_id' => $this->getUserId(),
            'content' => $this->getContent(),
            'created_at' => $this->getCreatedAt(),
        ];
    }
}