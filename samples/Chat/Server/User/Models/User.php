<?php

namespace Samples\Chat\Server\User\Models;

class User implements \JsonSerializable
{
    private int $id;
    private string $name;
    private string $username;
    private string $password;

    /**
     * @param int $id
     * @param string $name
     * @param string $username
     * @param string $password
     */
    public function __construct(int $id, string $name, string $username, string $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName()
        ];
    }
}