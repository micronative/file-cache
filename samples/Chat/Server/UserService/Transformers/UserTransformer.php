<?php

namespace Samples\Chat\Server\UserService\Transformers;

use Samples\Chat\Server\UserService\Models\User;

class UserTransformer
{
    public function transform(string|array $data): User
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return new User($data['id'], $data['name'], $data['username'], $data['password']);
    }
}
