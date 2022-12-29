<?php

namespace Samples\Chat\Server\PublicApi\Transformers;

use Samples\Chat\Server\PublicApi\Models\Conversation;

class ConversationTransformer
{
    private UserTransformer $userTransformer;
    private MessageTransformer $messageTransformer;

    /**
     * @param UserTransformer|null $userTransformer
     * @param MessageTransformer|null $messageTransformer
     */
    public function __construct(UserTransformer $userTransformer = null, MessageTransformer $messageTransformer = null)
    {
        $this->userTransformer = $userTransformer ?? new UserTransformer();
        $this->messageTransformer = $messageTransformer ?? new MessageTransformer();
    }

    /**
     * @param string|array $conversationData
     * @param string|array $userData
     * @return Conversation
     */
    public function transform(string|array $conversationData, string|array $userData): Conversation
    {
        if (is_string($conversationData)) {
            $conversationData = json_decode($conversationData, true);
        }

        $messages = [];
        foreach ($conversationData['messages'] as $message) {
            $messages[] = $this->messageTransformer->transform($message);
        }

        if (is_string($userData)) {
            $userData = json_decode($userData, true);
        }
        $participants = [];
        foreach ($userData as $user) {
            $participants[] = $this->userTransformer->transform($user);
        }

        return new Conversation($conversationData['id'], $participants, $messages);
    }
}