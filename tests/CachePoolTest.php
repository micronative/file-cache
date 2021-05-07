<?php
declare(strict_types=1);

namespace Tests\Cache\FileCache;

use Micronative\FileCache\Exceptions\CachePoolException;
use Micronative\FileCache\CacheItem;
use Micronative\FileCache\CachePool;
use PHPUnit\Framework\TestCase;

class CachePoolTest extends TestCase
{
    /** @var \Micronative\FileCache\CachePool */
    protected $cache;

    /** @var \Micronative\FileCache\CacheItem[] */
    protected $items;

    protected function setUp(): void
    {
        parent::setUp();
        $cacheTime = time();
        $tomorrow = new \DateTime('tomorrow');
        $yesterday = new \DateTime('yesterday');

        $data1 = [
            'key' => 'key1',
            'hitCount' => 2,
            'cacheTime' => $cacheTime,
            'expiresAt' => $tomorrow,
            'expiresAfter' => 3600,
            'value' => 'value'
        ];

        $data2 = [
            'key' => 'key2',
            'hitCount' => 2,
            'cacheTime' => $cacheTime,
            'expiresAt' => $tomorrow,
            'expiresAfter' => 3600,
            'value' => 'value'
        ];

        $data3 = [
            'key' => 'key3',
            'hitCount' => 3,
            'cacheTime' => $cacheTime,
            'expiresAt' => $yesterday,
            'expiresAfter' => 3600,
            'value' => 'value'
        ];

        $item1 = new CacheItem($data1);
        $item2 = new CacheItem($data2);
        $item3 = new CacheItem($data3);
        $this->items = [$item1, $item2, $item3];
        $cacheDir = __DIR__. '/assets/caches';
        $this->cache = new CachePool($cacheDir);
    }

    /**
     * @covers \Micronative\FileCache\CachePool::__construct
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function testConstruct()
    {
        $cacheDir = 'invalidDir';
        $this->expectException(CachePoolException::class);
        $this->expectExceptionMessage(CachePoolException::ERROR_INVALID_CACHE_DIR . $cacheDir);
        $this->cache = new CachePool($cacheDir);
    }

    /**
     * @covers \Micronative\FileCache\CachePool::__construct
     * @covers \Micronative\FileCache\CachePool::setCacheDir
     * @covers \Micronative\FileCache\CachePool::getCacheDir
     * @covers \Micronative\FileCache\CachePool::setItemPool
     * @covers \Micronative\FileCache\CachePool::getItemPool
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function testSettersAndGetters()
    {
        $cacheDir = __DIR__;
        $this->cache = new CachePool($cacheDir);
        $this->assertEquals($cacheDir, $this->cache->getCacheDir());

        $cacheDir = __DIR__ . '/caches';
        $this->cache->setCacheDir($cacheDir);
        $this->assertEquals($cacheDir, $this->cache->getCacheDir());

        $this->cache->setItemPool($this->items);
        $this->assertEquals($this->items, $this->cache->getItemPool());
        $this->cache->clear();
    }

    /**
     * @covers \Micronative\FileCache\CachePool::hashKey
     */
    public function testHashKey()
    {
        $key = 'key1';
        $hash = $this->cache->hashKey($key);
        $this->assertEquals(md5($key), $hash);
    }

    /**
     * @covers \Micronative\FileCache\CachePool::save
     * @covers \Micronative\FileCache\CachePool::hasItem
     * @covers \Micronative\FileCache\CachePool::getItem
     * @covers \Micronative\FileCache\CachePool::deleteItem
     * @covers \Micronative\FileCache\CachePool::getItemPool
     * @covers \Micronative\FileCache\CachePool::saveDeferred
     * @covers \Micronative\FileCache\CachePool::clear
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function testSave()
    {
        $cacheTime = time();
        $tomorrow = new \DateTime('tomorrow');
        $data4 = [
            'key' => 'key4',
            'hitCount' => 2,
            'cacheTime' => $cacheTime,
            'expiresAt' => $tomorrow,
            'expiresAfter' => 3600,
            'value' => 'value'
        ];
        $item4 = new CacheItem($data4);
        $this->cache->save($item4);
        $this->assertTrue($this->cache->hasItem('key4'));
        $get = $this->cache->getItem('key4');
        $this->assertEquals($item4, $get);
        $this->cache->deleteItem('key4');
        $this->assertEquals([], $this->cache->getItemPool());
        $this->cache->saveDeferred($item4);
        $get = $this->cache->getItem('key4');
        $this->assertEquals($item4, $get);
        $this->cache->clear();
    }

    /**
     * @covers \Micronative\FileCache\CachePool::save
     * @covers \Micronative\FileCache\CachePool::getItems
     * @covers \Micronative\FileCache\CachePool::deleteItems
     * @covers \Micronative\FileCache\CachePool::getItemPool
     * @covers \Micronative\FileCache\CachePool::clear
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function testGetItems()
    {
        $items = [];
        foreach ($this->items as $item) {
            $this->cache->save($item);
            $items[$item->getKey()] = $item;
        }
        $getItems = $this->cache->getItems(['key1', 'key2', 'key3']);
        $this->assertEquals($items, $getItems);

        $this->cache->deleteItems(['key1', 'key2','key3']);
        $this->assertEquals([], $this->cache->getItemPool());
        $this->cache->clear();
    }

    /**
     * @covers \Micronative\FileCache\CachePool::getItem
     * @covers \Micronative\FileCache\CachePool::retrieve
     * @covers \Micronative\FileCache\CachePool::clear
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function testRetrieve()
    {
        $item4 = $this->cache->getItem('key4');
        $this->assertEquals(null, $item4->getKey());

        $item = $this->cache->getItem('key1');
        $this->assertEquals('key1', $item->getKey());
        $this->cache->clear();
    }

    /**
     * @covers \Micronative\FileCache\CachePool::setItemPool
     * @covers \Micronative\FileCache\CachePool::save
     * @covers \Micronative\FileCache\CachePool::getCacheDir
     * @covers \Micronative\FileCache\CachePool::commit
     * @covers \Micronative\FileCache\CachePool::__destruct
     * @throws \Micronative\FileCache\Exceptions\CachePoolException
     */
    public function testCommit()
    {
        $this->cache->setItemPool([]);
        $commit = $this->cache->commit();
        $this->assertEquals(true, $commit);

        foreach ($this->items as $item) {
            $this->cache->save($item);
        }
        $file1 = $this->cache->getCacheDir() . DIRECTORY_SEPARATOR . md5('key1');
        $file2 = $this->cache->getCacheDir() . DIRECTORY_SEPARATOR . md5('key2');
        unset($this->cache);

        $this->assertFileExists($file1);
        $this->assertFileExists($file2);
    }
}
