<?php
declare(strict_types=1);

namespace Micronative\FileCache\Exceptions;

use Psr\Cache\CacheException;

class CachePoolException extends \Exception implements CacheException
{
    const ERROR_INVALID_CACHE_DIR = 'Invalid cache dir: ';
    const ERROR_FAILED_ENCODE_DATA = 'Failed to encode data: ';
    const ERROR_FAILED_TO_PUT_CONTENT = 'Failed to put file content';
    const ERROR_FAILED_TO_GET_CONTENT = 'Failed to get file content';
}
