<?php

namespace Samples\Chat\Server\UserService;

use Samples\Chat\Server\UserService\Transformers\UserTransformer;

class UserService
{
    private array $userDatabase;
    private UserTransformer $userTransformer;

    public function __construct(UserTransformer $userTransformer = null)
    {
        $this->userTransformer = $userTransformer ?? new UserTransformer();
        $this->userDatabase = require_once('Database/users.php');
    }

    public function authenticate(string $username, string $password)
    {
        foreach ($this->userDatabase as $data) {
            if ($data['username'] == $username && $data['password'] == $password) {

                return json_encode($data);
            }
        }

        return '';
    }

    public function getUsers(array $userIds)
    {
        $userData = array_filter($this->userDatabase, function ($data) use ($userIds) {
            return in_array($data['id'], $userIds);
        });

        $user = array_map(function ($data) {
            return $this->userTransformer->transform($data);
        }, $userData);

        return json_encode($user);
    }
}