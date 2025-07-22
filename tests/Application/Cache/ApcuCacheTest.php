<?php

declare(strict_types=1);

namespace Tests\Application\Cache;

use App\Application\Cache\ApcuCache;
use App\Application\Cache\CacheEntryNotFoundException;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class ApcuCacheTest extends TestCase {

  private ApcuCache $cache;

  protected function setUp(): void {
    apcu_clear_cache();
    $this->cache = new ApcuCache();
  }

  protected function tearDown(): void {
    apcu_clear_cache();
  }

  // Cache store tests
  public function testCacheAddWorks() {
    $key = 'testKey';
    $value = 'some very important value';

    $this->cache->set($key, $value);

    $stuff = false;
    assertSame(apcu_fetch($key, $stuff), $value);
    assertTrue($stuff);
  }

  public function testCacheUpdateWorks() {
    $key = 'test key';
    $value = 'some very important value';
    apcu_add($key, 'some initial value');

    $this->cache->set($key, $value);

    $stuff = false;
    assertSame(apcu_fetch($key, $stuff), $value);
    assertTrue($stuff);
  }

  // Cache get tests
  public function testCacheGetWorks() {
    $key = 'test key';
    $value = 'some very important value';
    apcu_add($key, $value);

    $fetched = $this->cache->get($key);

    assertSame($fetched, $value);
  }

  public function testCacheGetNonexistentKey() {
    $key = 'test key';
    $this->expectException(CacheEntryNotFoundException::class);

    $this->cache->get($key);
  }

  // Exists tests
  public function testCacheExists() {
    $key = 'test key';
    apcu_add($key, 'value');

    assertTrue($this->cache->exists($key));
    assertFalse($this->cache->exists('nonexistentkey'));
  }

  // Delete tests
  public function testCacheDelete() {
    $key = 'test key';
    apcu_add($key, 'value');

    $this->cache->del($key);

    assertFalse(apcu_exists($key));
  }

  public function testCacheDeleteNonexistentKey() {
    $key = 'test key';
    $this->expectException(CacheEntryNotFoundException::class);

    $this->cache->del($key);
  }

  // Pop tests
  public function testCachePop() {
    $key = 'test key';
    $value = 'some value';
    apcu_add($key, $value);

    $fetched = $this->cache->pop($key);

    assertSame($fetched, $value);
    assertFalse(apcu_exists($key));
  }

  public function testCachePopNonexistentKey() {
    $key = 'test key';
    $this->expectException(CacheEntryNotFoundException::class);

    $this->cache->pop($key);
  }

  // Clear tests
  public function testCacheClear() {
    $key = 'test key';
    $value = 'some value';
    apcu_add($key, $value);

    $this->cache->clear();

    $iter = new \APCUIterator();
    $count = $iter->getTotalCount();
    assertSame($count, 0);
  }

}
