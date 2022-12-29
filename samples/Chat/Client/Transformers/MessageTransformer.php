<?php

namespace Samples\Chat\Client\Transformers;

use Samples\Chat\Client\Models\Message;

class MessageTransformer
{
    public function transform(string|array $data): Message
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return new Message($data['order'], $data['user_id'], $data['content'], $data['created_at']);
    }
}