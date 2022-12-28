<?php

namespace Samples\Chat\Models;

class User implements \JsonSerializable
{
    private int $id;
    private string $name;
    private string $username;
    private string $password;

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

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'password' => $this->password,
        ];
    }
}