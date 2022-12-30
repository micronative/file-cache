<?php

namespace Samples\Chat\Server\Chat\Services;

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