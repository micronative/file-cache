<?php

namespace Samples\Chat\Client\Transformers;

use Samples\Chat\Client\Models\User;

class UserTransformer
{
    public function transform(string|array $data): User
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return new User($data['id'], $data['name']);
    }
}
