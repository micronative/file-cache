<?php
declare(strict_types=1);

namespace Tests\Cache\FileCache;

use Micronative\FileCache\CacheItem;
use PHPUnit\Framework\TestCase;

class CacheItemTest extends TestCase
{
    /** @var \Micronative\FileCache\CacheItem */
    protected $cacheItem;

    /**
     * @covers \Micronative\FileCache\CacheItem::__construct
     * @covers \Micronative\FileCache\CacheItem::setData
     * @covers \Micronative\FileCache\CacheItem::intervalToSeconds
     * @covers \Micronative\FileCache\CacheItem::isNotExpired
     * @covers \Micronative\FileCache\CacheItem::set
     * @covers \Micronative\FileCache\CacheItem::setIsHit
     * @covers \Micronative\FileCache\CacheItem::expiresAt
     * @covers \Micronative\FileCache\CacheItem::setKey
     * @covers \Micronative\FileCache\CacheItem::expiresAfter
     * @covers \Micronative\FileCache\CacheItem::cachesAt
     * @covers \Micronative\FileCache\CacheItem::setHitCount
     * @covers \Micronative\FileCache\CacheItem::get
     * @covers \Micronative\FileCache\CacheItem::getExpiresAfter
     * @covers \Micronative\FileCache\CacheItem::getCachesAt
     * @covers \Micronative\FileCache\CacheItem::getExpiresAt
     * @covers \Micronative\FileCache\CacheItem::getKey
     * @covers \Micronative\FileCache\CacheItem::getHitCount
     * @covers \Micronative\FileCache\CacheItem::isHit
     */
    public function testSettersAndGetters()
    {
        $cacheTime = time();
        $tomorrow = new \DateTime('tomorrow');
        $data = [
            'key' => 'key',
            'hitCount' => 2,
            'cacheTime' => $cacheTime,
            'expiresAt' => $tomorrow,
            'expiresAfter' => 3600,
            'value' => 'value'
        ];
        $this->cacheItem = new CacheItem($data);
        $this->assertEquals($data['key'], $this->cacheItem->getKey());
        $this->assertEquals($data['hitCount'], $this->cacheItem->getHitCount());
        $this->assertEquals($tomorrow->getTimestamp(), $this->cacheItem->getExpiresAt());
        $this->assertEquals($data['expiresAfter'], $this->cacheItem->getExpiresAfter());
        $this->assertEquals($data['value'], $this->cacheItem->get());
        $this->assertEquals($data['cacheTime'], $this->cacheItem->getCachesAt());

        $tomorrow = new \DateTime('tomorrow');
        $this->cacheItem
            ->setIsHit(true)
            ->set('newValue')
            ->setHitCount(3)
            ->cachesAt($cacheTime)
            ->expiresAfter(7200)
            ->setKey('newKey')
            ->expiresAt($tomorrow);

        $this->assertEquals(true, $this->cacheItem->isHit());
        $this->assertEquals('newValue', $this->cacheItem->get());
        $this->assertEquals(3, $this->cacheItem->getHitCount());
        $this->assertEquals($cacheTime, $this->cacheItem->getCachesAt());
        $this->assertEquals(7200, $this->cacheItem->getExpiresAfter());
        $this->assertEquals('newKey', $this->cacheItem->getKey());
        $this->assertEquals($tomorrow->getTimestamp(), $this->cacheItem->getExpiresAt());

        $yesterday = new \DateTime('yesterday');
        $oneHour = new \DateInterval('PT1H');
        $this->cacheItem->expiresAt($yesterday);
        $this->assertEquals(false, $this->cacheItem->isNotExpired());
        $this->cacheItem->setIsHit(true);
        $isHit = $this->cacheItem->isHit();
        $this->assertEquals(false, $isHit);

        $expiredAt = new \DateTime(date("Y-m-d", strtotime("+2 day")));
        $this->cacheItem
            ->cachesAt($yesterday)
            ->expiresAfter($oneHour)
            ->expiresAt($expiredAt);
        $this->assertEquals(false, $this->cacheItem->isNotExpired());

        $this->cacheItem->expiresAt(null);
        $this->assertEquals(false, $this->cacheItem->isNotExpired());
    }

    /**
     * @covers \Micronative\FileCache\CacheItem::__construct
     * @covers \Micronative\FileCache\CacheItem::setData
     * @covers \Micronative\FileCache\CacheItem::toArray
     */
    public function testToArray()
    {
        $cacheTime = time();
        $now = new \DateTime('now');
        $data = [
            'key' => 'key',
            'hitCount' => 2,
            'cacheTime' => $cacheTime,
            'expiresAt' => $now,
            'expiresAfter' => 3600,
            'value' => 'value'
        ];
        $expected = [
            'key' => 'key',
            'hitCount' => 2,
            'cacheTime' => $cacheTime,
            'expiresAt' => $now->getTimestamp(),
            'expiresAfter' => 3600,
            'value' => 'value'
        ];
        $this->cacheItem = new CacheItem($data);
        $array = $this->cacheItem->toArray();
        $this->assertEquals($expected, $array);
    }
}
