<?php

namespace Samples\Chat\Services;

use Samples\Chat\Models\User;

class UserService
{
    private array $userDatabase = [
        ['id' => 1, 'name' => 'Ken', 'username' => 'ken@chat.com', 'password' => '123'],
        ['id' => 2, 'name' => 'May', 'username' => 'may@chat.com', 'password' => '123'],
        ['id' => 3, 'name' => 'Tif', 'username' => 'tif@chat.com', 'password' => '123'],
    ];

    public function authenticate(string $username, string $password)
    {
        foreach ($this->userDatabase as $data) {
            if ($data['username'] == $username && $data['password'] == $password) {

                return json_encode($data);
            }
        }

        return false;
    }
}