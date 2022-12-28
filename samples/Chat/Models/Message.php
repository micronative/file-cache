<?php

namespace Samples\Chat\Models;

class Message implements \JsonSerializable
{
    private int $order;
    private int $userId;
    private string $content;

    public function __construct(int $order, int $userId, string $content)
    {
        $this->order = $order;
        $this->userId = $userId;
        $this->content = $content;
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


    public function jsonSerialize()
    {
        return [
            'order' => $this->getOrder(),
            'user_id' => $this->getUserId(),
            'content' => $this->getContent(),
        ];
    }
}