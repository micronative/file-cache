<?php

namespace Samples\Chat\Client\Display;

class MessageMapper
{
    /**
     * @param array $messageData
     * @param array $userData
     * @return array
     */
    public function map(array $messageData, array $userData): array
    {
        return array_map(function ($message) use ($userData) {
            foreach ($userData as $user) {
                if ($message['user_id'] == $user['id']) {
                    return [
                        'order' => $message['order'],
                        'created_at' => $message['created_at'],
                        'user' => $user['name'],
                        'content' => $message['content'],
                    ];
                }
            }
        }, $messageData);
    }
}