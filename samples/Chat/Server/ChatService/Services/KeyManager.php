<?php

namespace Samples\Chat\Server\ChatService\Services;

class KeyManager
{
    /**
     * @param array $participants
     * @return string
     */
    public function key(array $participants): string
    {
        sort($participants);
        return md5(implode($participants));
    }
}