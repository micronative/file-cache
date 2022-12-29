<?php

namespace Samples\Chat\Server\Transformers;

use Samples\Chat\Server\Models\Message;

class MessageTransformer
{
    public function transform(string|array $data): Message
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return new Message($data['order'], $data['user_id'], $data['content']);
    }
}