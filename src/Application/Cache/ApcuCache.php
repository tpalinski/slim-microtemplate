<?php

declare(strict_types=1);

namespace App\Application\Cache;

use App\Application\Cache\CacheException;

class ApcuCache implements CacheInterface {

  public function get(string $key): mixed
  {
    if(!apcu_exists($key)) {
      throw new CacheEntryNotFoundException("No entry under provided key");
    }
    $fetchSuccesful = false;
    $res = apcu_fetch($key, $fetchSuccesful);
    if(!$fetchSuccesful) {
      throw new CacheException("Could not fetch existing value from apcu");
    }
    return $res;
  }

  public function set(string $key, mixed $value, int $ttl = 60)
  {
    $successful = apcu_store($key, $value, $ttl);
    if(!$successful) {
      throw new CacheException("Could not set the value");
    }
  }

  public function exists(string $key): bool
  {
    return apcu_exists($key);
  }

  public function del(string $key)
  {
    if(!apcu_exists($key)) {
      throw new CacheEntryNotFoundException("No entry under provided key to be deleted");
    }
    $successful = apcu_delete($key);
    if(!$successful) {
      throw new CacheException("Could not delete the desired value");
    }
  }

  public function pop(string $key): mixed
  {
    if(!apcu_exists($key)) {
      throw new CacheEntryNotFoundException("No entry under provided key to be deleted");
    }
    $fetchSuccesful = false;
    $res = apcu_fetch($key, $fetchSuccesful);
    if(!$fetchSuccesful) {
      throw new CacheException("Could not fetch existing value from apcu");
    }
    $delSuccessful = apcu_delete($key);
    if(!$delSuccessful) {
      throw new CacheException("Could not delete the desired value");
    }
    return $res;
  }

  public function clear()
  {
    apcu_clear_cache();
  }
}
