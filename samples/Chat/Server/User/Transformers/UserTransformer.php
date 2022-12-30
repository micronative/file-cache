<?php

namespace Samples\Chat\Server\User\Transformers;

use Samples\Chat\Server\User\Models\User;

class UserTransformer
{
    /**
     * @param string|array $data
     * @return User
     */
    public function transform(string|array $data): User
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return new User($data['id'], $data['name'], $data['username'], $data['password']);
    }
}
