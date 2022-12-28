<?php

namespace Samples\Chat\Transformers;

use Samples\Chat\Exceptions\UserException;
use Samples\Chat\Models\User;

class UserTransformer
{
    public function transform(string|array $data): User
    {
        try {
            if (is_string($data)) {
                $data = json_decode($data, true);
            }

            return new User($data['id'], $data['name'], $data['username'], $data['password']);
        } catch (\JsonException $e) {
            throw new UserException('Invalid json data');
        }
    }
}
