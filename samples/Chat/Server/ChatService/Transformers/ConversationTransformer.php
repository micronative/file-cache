<?php

namespace Samples\Chat\Server\ChatService\Transformers;

use Samples\Chat\Server\ChatService\Models\Conversation;
use Samples\Chat\Server\ChatService\Models\Message;

class ConversationTransformer
{
    public function transform(string|array $data): Conversation
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $messages = [];
        foreach ($data['messages'] as $message) {
            $messages[] = new Message($message['order'], $message['user_id'], $message['content']);
        }

        return new Conversation($data['id'], $data['participant_ids'], $messages);
    }
}