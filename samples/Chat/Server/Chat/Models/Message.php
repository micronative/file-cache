<?php

namespace Samples\Chat\Server\Chat\Models;

class Message implements \JsonSerializable
{
    private int $order;
    private int $userId;
    private string $content;
    private string $createdAt;

    /**
     * @param int $order
     * @param int $userId
     * @param string $content
     */
    public function __construct(int $order, int $userId, string $content)
    {
        $this->order = $order;
        $this->userId = $userId;
        $this->content = $content;
        $this->createdAt = date('Y:m:d H:i:s');
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

    /**
     * @return array
     */
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