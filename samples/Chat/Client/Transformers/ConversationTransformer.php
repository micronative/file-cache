<?php

namespace Samples\Chat\Client\Transformers;

use Samples\Chat\Client\Models\Conversation;
use Samples\Chat\Client\Models\Message;
use Samples\Chat\Server\Models\User;

class ConversationTransformer
{
    private UserTransformer $userTransformer;
    private MessageTransformer $messageTransformer;

    public function __construct(UserTransformer $userTransformer = null, MessageTransformer $messageTransformer = null)
    {
        $this->userTransformer = $userTransformer ?? new UserTransformer();
        $this->messageTransformer = $messageTransformer ?? new MessageTransformer();
    }

    public function transform(string|array $data): Conversation
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $messages = [];
        foreach ($data['messages'] as $message) {
            $messages[] = $this->messageTransformer->transform($message);
        }

        $participants = [];
        foreach ($data['participants'] as $participant) {
            $participants[] = $this->userTransformer->transform($participant);
        }

        return new Conversation($data['id'], $participants, $messages);
    }
}