<?php

namespace Samples\Chat\Client\Display;

class MessageMapper
{
    public function map(array $messageData, array $userData)
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