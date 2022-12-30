<?php

namespace Samples\Chat\Server\Chat\Transformers;

use Samples\Chat\Server\Chat\Models\Message;

class MessageTransformer
{
    /**
     * @param string|array $data
     * @return Message
     */
    public function transform(string|array $data): Message
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        return new Message($data['order'], $data['user_id'], $data['content']);
    }
}