<?php

namespace Samples\Chat\Server\Chat\Transformers;

use Samples\Chat\Server\Chat\Models\Conversation;

class ConversationTransformer
{
    private MessageTransformer $messageTransformer;

    /**
     * @param MessageTransformer|null $messageTransformer
     */
    public function __construct(MessageTransformer $messageTransformer = null)
    {
        $this->messageTransformer = $messageTransformer ?? new MessageTransformer();
    }

    /**
     * @param string|array $data
     * @return Conversation
     */
    public function transform(string|array $data): Conversation
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $messages = [];
        foreach ($data['messages'] as $message) {
            $messages[] = $this->messageTransformer->transform($message);
        }

        return new Conversation($data['id'], $data['participant_ids'], $messages);
    }
}