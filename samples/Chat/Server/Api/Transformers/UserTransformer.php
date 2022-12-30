<?php

namespace Samples\Chat\Server\Api\Transformers;

use Samples\Chat\Server\Api\Models\User;

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

        return new User($data['id'], $data['name']);
    }
}
