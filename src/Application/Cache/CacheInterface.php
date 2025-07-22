<?php

declare(strict_types=1);

namespace App\Application\Cache;

interface CacheInterface
{
    /**
     * Adds the value to cache, overwriting the existing value
     * @param string $key
     * @param mixed $value
     * @param int $ttl Time-to-live in seconds, defaults to 60
     * @throws CacheException
     * @return void
     */
    public function set(string $key, mixed $value, int $ttl = 60);

    /**
     * Fetches the value from cache.
     * @param string $key
     * @return mixed
     * @throws CacheEntryNotFoundException
     */
    public function get(string $key): mixed;

    /**
     * Fetches the value from the storage and deletes it from the storage
     * @param string $key
     * @return mixed
     * @throws CacheEntryNotFoundException
     */
    public function pop(string $key): mixed;

    /**
     * Checks whether an entry under given key exists in cache
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * Deletes the entry under the given key
     * @param string $key
     * @return void
     * @throws CacheEntryNotFoundException
     */
    public function del(string $key);

    /**
     * Clears the entire cache store
     * @throws CacheException
     */
    public function clear(): void;

}
